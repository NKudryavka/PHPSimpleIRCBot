<?php

class Server {
    private $sock = null;
    private $setting;
    private $name = '';
    private $modules = [];
    private $mode;
    const TIMEOUT = 300; // PING間隔のタイムアウト（秒）

    public static function forge($name, $setting) {
        return new self($name, $setting);
    }

    private function __construct($name, $setting) {
        $this->setting = $setting;
        $this->name = $name;
        if(isset($setting['modules'])) {
            foreach($setting['modules'] as $name => $opt) {
                $fullname = 'Modules\\'.ucfirst($name).'\\'.ucfirst($name);
                $this->modules[$name] = new $fullname($this, $opt);
            }
        }
    }

    public function __get($name) {
        if($name === 'name') return $this->name;
    }

    public function connect() {
        $this->sock = fsockopen($this->setting['host'], $this->setting['port']);
        if(!$this->sock) return null;
        $this->send('USER '.$this->setting['loginname'].' 8 0 :'.$this->setting['realname']);
        $this->send('NICK '.$this->setting['nick']);
        $this->join();

        $str = $this->recieve();
        $lastPing = time();
        while(!feof($this->sock)) {
            Log::debug($str);
            if($str === '' and time() - $lastPing > self::TIMEOUT) break;
            if(mb_strpos($str, 'PING') === 0) {
                $lastPing = time();
                $str = explode(':', $str);
                $this->onPing();
                if(!$this->send('PONG '.$str[1])) {
                    break;
                }
            } else {
                @list($user, $type, $target, $content) = explode(' ', $str);
                if($type === '433') {
                    // Nick already exist
                    $this->setting['nick'] .= '_';
                    $this->send('NICK '.$this->setting['nick']);
                    $this->join();
                }
                if($type === 'PRIVMSG' && mb_strpos($content, "\x01") !== 1) {
                    $this->onMessage(rtrim(mb_substr($user, 1, mb_strpos($user, '!')-1), "_"), $target, mb_substr($str, mb_strpos($str, ' :')+2));
                }
            }

            $str = $this->recieve();
        }

        echo "Disconnected\r\n";
        fclose($this->sock);
    }

    private function recieve() {
        return trim(fgets($this->sock, 1024), "\r\n\t\0");
    }

    private function send($str) {
        return fputs($this->sock, $str."\r\n");
    }

    private function join() {
        foreach($this->setting['channels'] as $channel) {
            $this->send('JOIN '.$this->encode($channel));
        }
    }

    public function encode($str) {
        if(isset($this->setting['encoding'])) {
            return mb_convert_encoding($str, $this->setting['encoding'], 'UTF-8');
        } else {
            return $str;
        }
    }

    public function decode($str) {
        if(isset($this->setting['encoding'])) {
            return mb_convert_encoding($str, 'UTF-8', $this->setting['encoding']);
        } else {
            return $str;
        }
    }

    private function onPing() {
        foreach ($this->modules as $mod) {
            $mod->onPing();
        }
    }

    private function onMessage($nick, $target, $message) {
        foreach ($this->modules as $mod) {
            $mod->onMessage($nick, $this->decode($target), $this->decode($message));
        }
    }

    public function sendNotice($channel, $str) {
        $this->send('NOTICE '.$this->encode($channel).' :'.$this->encode($str));
    }

    public function __destruct() {
        if($this->sock) {
            fclose($this->sock);
        }
    }
}