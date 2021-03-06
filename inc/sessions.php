<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

final class session {
    protected $db;
    protected $memcached;
    protected $_prefix;
    protected $_ttl = 3600;
    protected $_lockTimeout = 10;
    public static $securityKey_mcrypt = '(fk2N7S!(Bd';

    function __construct() {
        $this->_prefix = '_';
        $this->_skcrypt = '';
        $this->_use_sqlS_local = false;

        switch(sessions_backend) {
            case 'memcache':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use Memcache for Sessions");

                session_set_save_handler(array($this, 'mem_open'), array($this, 'mem_close'), array($this, 'mem_read'), array($this, 'mem_write'), array($this, 'mem_destroy'), array($this, 'mem_gc'));
                register_shutdown_function('session_write_close');
            break;
            case 'apc':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use APC for Sessions");

                $this->_ttl = sessions_ttl_maxtime;
                session_set_save_handler(array($this, 'apc_open'), array($this, 'apc_close'), array($this, 'apc_read'), array($this, 'apc_write'), array($this, 'apc_destroy'), array($this, 'apc_gc'));
                register_shutdown_function('session_write_close');
            break;
            case 'mysql':
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use MySQL for Sessions");

                session_set_save_handler(array($this, 'sql_open'), array($this, 'sql_close'), array($this, 'sql_read'), array($this, 'sql_write'), array($this, 'sql_destroy'), array($this, 'sql_gc'));
                register_shutdown_function('session_write_close');
            break;
            default:
                if(show_sessions_debug)
                    DebugConsole::insert_info("session::__construct()", "Use PHP-Default for Sessions");
        }
    }

    public final function init($destroy=false) {
        if(!headers_sent() && !$this->is_session_started()) {
            if(show_sessions_debug)
                DebugConsole::insert_info("session::init()", "Call session_start()");

            $cryptkey = false;
            if(file_exists(basePath.'/inc/cryptkey.php')) {
                require_once(basePath.'/inc/cryptkey.php');
                
                if(show_sessions_debug) {
                    DebugConsole::insert_info("session::init()", "Found 'inc/cryptkey.php'");
                    DebugConsole::insert_successful("session::init()", "Use Crypt Key for data encode");
                }
            } else
                if(show_sessions_debug)
                    DebugConsole::insert_error("session::init()", "Not found 'inc/cryptkey.php'");    

            self::$securityKey_mcrypt = (!$cryptkey ? self::$securityKey_mcrypt : $cryptkey);
            $this->_prefix = self::$securityKey_mcrypt.'_';
            $this->_skcrypt = (!$cryptkey ? self::$securityKey_mcrypt : $cryptkey);

            if(session_start())
                if(show_sessions_debug)
                    DebugConsole::insert_successful("session::init()", "Sessions started, ready to use");
        }

        if($destroy) {
            DebugConsole::insert_error("session::init()", "Sessions destroy & Regenerate");
            session_unset();
            session_destroy();
            session_regenerate_id(true);
        }

        if (in_array(sessions_encode_type, hash_algos()))
            ini_set('session.hash_function', sessions_encode_type);

        ini_set('session.hash_bits_per_character', 5);
        return true;
    }

    ###################################################
    ################ Memcache Backend #################
    ###################################################
    public final function mem_open() {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::mem_open()", "Connect to Memcache Server");

        if($this->memcached instanceOf Memcache) return false;
        $this->memcached = new Memcache();
        $this->memcached->addServer(sessions_memcache_host,sessions_memcache_port);

        if(show_sessions_debug) {
            if(!$this->memcached->getServerStatus(sessions_memcache_host, sessions_memcache_port)) {
                DebugConsole::insert_error("session::mem_open()", "Connect to Memcache Server failed!");
                DebugConsole::insert_error("session::mem_open()", "Host: ".sessions_memcache_host.':'.sessions_memcache_port);
            }
            else
                DebugConsole::insert_successful("session::mem_open()", "Connected to Memcache Server");
        }

        return !$this->memcached->getServerStatus(sessions_memcache_host, sessions_memcache_port) ? false : true;
    }

    public final function mem_close() {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::mem_close()", "Disconnect Memcache Server");

        return $this->memcached->close();
    }

    public final function mem_read($id) {
        global $db_array;
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::mem_read()", "Read Session-Data from Memcache");
            DebugConsole::insert_info("session::mem_read()", "Select ID: '".$id."'");
        }

        $data = $this->memcached->get($this->_prefix.$id);
        if(empty($data)) return '';

        if(sessions_encode)
            $data = self::decode($data,$this->_skcrypt,true);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::mem_read()", $data);

        return $data;
    }

    public final function mem_write($id, $data) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::mem_write()", "Write Session-Data to Memcache");
            DebugConsole::insert_info("session::mem_write()", "Select ID: '".$id."'");
        }

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::mem_write()", $data);

        if(sessions_encode)
            $data = self::encode($data,$this->_skcrypt,true);

        $result = $this->memcached->replace($this->_prefix.$id, $data, MEMCACHE_COMPRESSED, sessions_ttl_maxtime);
        if( $result == false )
            $result = $this->memcached->set($this->_prefix.$id, $data, MEMCACHE_COMPRESSED, sessions_ttl_maxtime);

        return $result;
    }

    public final function mem_destroy($id) {
        return $this->memcached->delete($this->_prefix.$id);
    }

    public final function mem_gc($max)
    { return true; }

    ###################################################
    ################### APC Backend ###################
    ###################################################
    public final function apc_open($savePath, $sessionName) {
        $this->_prefix = 'BSession/'.$sessionName;
        if (!apc_fetch($this->_prefix.'/TS', $result)) {
            apc_store($this->_prefix.'/TS', array(''));
            apc_store($this->_prefix.'/LOCK', array(''));
        }

        if(show_sessions_debug)
            DebugConsole::insert_info("session::apc_open()", "Set default store for APC");

        return true;
    }

    public final function apc_close() { return true; }

    public final function apc_read($id) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::apc_read()", "Read Session-Data from APC");
            DebugConsole::insert_info("session::apc_read()", "Select ID: '".$id."'");
        }

        $key = $this->_prefix.'/'.$id;
        if (!apc_fetch($key)) return '';

        if ($this->_ttl) {
            $ts = apc_fetch($this->_prefix.'/TS');
            if (empty($ts[$id])) return '';
            else if (!empty($ts[$id]) && $ts[$id] + $this->_ttl < time()) {
                unset($ts[$id]);
                apc_delete($key);
                apc_store($this->_prefix.'/TS', $ts);
                return '';
            }
        }

        if (!$this->_lockTimeout) {
            $locks = apc_fetch($this->_prefix.'/LOCK');
            if (!empty($locks[$id])) {
                while (!empty($locks[$id]) && $locks[$id] + $this->_lockTimeout >= time()) {
                    usleep(10000);
                    $locks = apc_fetch($this->_prefix.'/LOCK');
                }
            }

            $locks[$id] = time();
            apc_store($this->_prefix.'/LOCK', $locks);
        }

        $data = apc_fetch($key);

        if(sessions_encode)
            $data = self::decode($data,$this->_skcrypt,true);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::apc_read()", $data);

        return $data;
    }

    public final function apc_write($id, $data) {
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::apc_write()", "Write Session-Data to APC");
            DebugConsole::insert_info("session::apc_write()", "Select ID: '".$id."'");
        }

        $ts = apc_fetch($this->_prefix.'/TS');
        $ts[$id] = time();
        apc_store($this->_prefix.'/TS', $ts);

        $locks = apc_fetch($this->_prefix.'/LOCK');
        unset($locks[$id]);
        apc_store($this->_prefix.'/LOCK', $locks);

        if(show_sessions_debug)
            DebugConsole::insert_successful("session::apc_write()", $data);

        if(sessions_encode)
            $data = self::encode($data,$this->_skcrypt,true);

        return apc_store($this->_prefix.'/'.$id, $data, $this->_ttl);
    }

    public final function apc_destroy($id) {
        if(show_sessions_debug)
            DebugConsole::insert_info("session::apc_destroy()", "Call Session destroy");

        $ts = apc_fetch($this->_prefix.'/TS');
        unset($ts[$id]);
        apc_store($this->_prefix.'/TS', $ts);

        $locks = apc_fetch($this->_prefix.'/LOCK');
        unset($locks[$id]);
        apc_store($this->_prefix.'/LOCK', $locks);

        return apc_delete($this->_prefix.'/'.$id);
    }

    public final function apc_gc($lifetime) {
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::apc_gc()", "Call Garbage-Collection");
        }

        if ($this->_ttl) {
            $lifetime = min($lifetime, $this->_ttl);
        }

        $ts = apc_fetch($this->_prefix.'/TS');
        foreach ($ts as $id=>$time) {
            if ($time + $lifetime < time()) {
                apc_delete($this->_prefix.'/'.$id);
                unset($ts[$id]);
            }
        }

        return apc_store($this->_prefix.'/TS', $ts);
    }

    ###################################################
    ################## MySQL Backend ##################
    ###################################################
    public final function get_sql_resource() {
        if ($this->db->mysqli_resource instanceOf mysqli) {
            return $this->db->mysqli_resource;
        }
        
        return null;
    }

    public final function sql_open() {
        global $db;
        if (sessions_mysql_sethost) {
            $this->_use_sqlS_local = false;
            $this->db = new database(sessions_mysql_host,sessions_mysql_db,sessions_mysql_user,sessions_mysql_pass);
        } else {
            $this->_use_sqlS_local = true;
            $this->db = new database();
        }
        
        if(show_sessions_debug) {
            if ($this->db->mysqli_resource instanceOf mysqli === false) {
                DebugConsole::insert_error("session::sql_open()", "Connect to MySQL Server failed!");
                DebugConsole::insert_error("session::sql_open()", "Host: " . (sessions_mysql_sethost ? sessions_mysql_host : $db['host']));
                DebugConsole::insert_error("session::sql_open()", "User: " . (sessions_mysql_sethost ? sessions_mysql_user : $db['user']));
                DebugConsole::insert_error("session::sql_open()", "DB: " . (sessions_mysql_sethost ? sessions_mysql_db : $db['db']));
                echo DebugConsole::show_logs();
                exit('DZCP-Sessions: Connect to MySQL Server failed!');
            }
        }

        return ($this->db->mysqli_resource instanceOf mysqli === false);
    }

    public final function sql_close() {
        //Dummy
    }

    public final function sql_read($id) {
        global $db;
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::sql_read()", "Read Session-Data from Database");
            DebugConsole::insert_info("session::sql_read()", "Select ID: '".$id."'");
        }

        $data = '';
        if ($this->db->mysqli_resource instanceOf mysqli) {
            if(!$this->db->db_stmt("SELECT `data` FROM `" . $db['sessions'] . "` WHERE `ssid` = ? LIMIT 1;",array('s', $id),true)) { return ''; }
            $data = $this->read_stmt = $this->db->db_stmt("SELECT `data` FROM `" . $db['sessions'] . "` WHERE `ssid` = ? LIMIT 1;",array('s', $id),false,true);
            if (empty($data['data'])) { return ''; }

            if(sessions_encode) {
                $data = self::decode($data['data'], $this->_skcrypt, true);
            }

            if (show_sessions_debug) {
                DebugConsole::insert_successful("session::sql_read()", $data);
            }
        }

        return $data;
    }

    public final function sql_write($id, $data) {
        global $db;
        if(show_sessions_debug) {
            DebugConsole::insert_info("session::sql_write()", "Write Session-Data to Database");
            DebugConsole::insert_info("session::sql_write()", "Select ID: '".$id."'");
        }

        if (show_sessions_debug) {
            DebugConsole::insert_successful("session::sql_write()", $data);
        }

        if (sessions_encode) {
            $data = self::encode($data, $this->_skcrypt, true);
        }

        if ($this->db->mysqli_resource instanceOf mysqli) {
            $time = time();
            if(!$this->db->db_stmt("SELECT `id` FROM `".$db['sessions']."` WHERE `ssid` = ? LIMIT 1;",array('s', $id),true)) {
                return $this->db->db_stmt("INSERT INTO `".$db['sessions']."` (id, ssid, time, data) VALUES (NULL, ?, ?, ?);",array('sis', $id, $time, $data),true);
            } else {
                return $this->db->db_stmt("UPDATE `".$db['sessions']."` SET `time` = ?, `data` = ? WHERE `ssid` = ?;",array('iss', $time, $data, $id),true);
            }
        }

        return false;
    }

    public final function sql_destroy($id) {
        global $db;
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::sql_destroy()", "Call Session destroy");
        }

        return $this->db->db_stmt("DELETE FROM `" . $db['sessions'] . "` WHERE `ssid` = ?;",array('s', $id));
    }

    public final function sql_gc($max) {
        global $db;
        if (show_sessions_debug) {
            DebugConsole::insert_info("session::sql_gc()", "Call Garbage-Collection");
        }

        $new_time = time() - $max;
        return $this->db->db_stmt("DELETE FROM `".$db['sessions']."` WHERE `time` < ?;",array('s', $new_time));
    }

    public static function encode($data,$mcryptkey='',$binary=false,$hex=false) {
        $crypt = new Crypt(CRYPT_MODE_BASE64,CRYPT_HASH_SHA1);
       
        if (empty($mcryptkey)) { $crypt->__set('Key', self::$securityKey_mcrypt); } 
        else { $crypt->__set('Key', $mcryptkey); }

        if($binary && !$hex) { $crypt->__set('Hash',CRYPT_MODE_BINARY); }
        if(!$binary && $hex) { $crypt->__set('Hash',CRYPT_MODE_HEXADECIMAL); }
        $is_array = is_array($data);
        $data = serialize(array('data' => $data, 'array' => $is_array));
        return $crypt->Encrypt($data);
    }

    public static function decode($data,$mcryptkey='',$binary=false,$hex=false) {
        $crypt = new Crypt(CRYPT_MODE_BASE64,CRYPT_HASH_SHA1);
        
        if (empty($mcryptkey)) { $crypt->__set('Key', self::$securityKey_mcrypt); } 
        else { $crypt->__set('Key', $mcryptkey); }

        if($binary && !$hex) { $crypt->__set('Hash',CRYPT_MODE_BINARY); }
        if(!$binary && $hex) { $crypt->__set('Hash',CRYPT_MODE_HEXADECIMAL); }
        $data = unserialize($crypt->Decrypt($data));
        if (!is_array($data)) {
            return null;
        }
        return $data['data'];
    }
    
    ###################################################
    ##################### Private #####################
    ###################################################

    protected final function is_session_started() {
        if ( php_sapi_name() !== 'cli' ) {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }

        return false;
    }
}