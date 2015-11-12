#!/usr/bin/python
# coding=utf-8
# author: jm
#
"""
    Test ufal handles.

    This should be a nagios plugin which takes a file as the first input parameter
    and line by line tries to access the handles in specified timeout.

    see http://svn.ms.mff.cuni.cz/redmine/projects/dspace-modifications/wiki/MonitoringNagios
"""
import httplib
import sys
import time
import urllib2
import socket
from datetime import datetime

_OK = 0
_WARN = 1
_EXC = 2
_EXC_SOFT = 3

_env = {
    #
    "NAGIOS_VALS": {
        0: [40,60,0,60],
        1: [40,60,0,60],
        2: (0,1,0,1),
        3: (0,1,0,90),
    },
    #
    "log_file": None,

    #
    "timeout": None,
    "timeout_mul": 4,
    "timeout_soft": 2,

    #
    "keys_in_resp": (".mff.cuni.cz", "only.testing"),
}


def _log(msg):
    with open(_env["log_file"], "a+") as fout:
        msg = str(datetime.now()) + " " + msg + "\n"
        fout.write(msg)

def _exit( code, msg_str, time_d ):
    """ 
      Exit with proper exit code 
      warn crit min max
    
    """
    warn_time, crit_time, min_time, max_time = _env["NAGIOS_VALS"][code]

    msg_whole = "%s total time [%ss] with ret code [%s]|time=%ss;%s;%s;%s;%s\n" % (
      msg_str, time_d, code, round(float(time_d), 1), warn_time, crit_time, min_time, max_time)
    print( msg_whole )
    _log(msg_whole)
    sys.exit(code)


def get_ip(conn):
    #sock = socket.fromfd(http_response.fp.fileno(), socket.AF_INET, socket.SOCK_STREAM)
    #return sock.getpeername()[0]
    return conn.sock.getpeername()[0]
   

def check_resolving_handle_timeout( handle, timeout ):
    """
        Try to resolve it
    """
    ret = _OK
    msg = None
    timeout_i = int(timeout) / 1000
    started_dt = str(datetime.now())
    took_s = time.time()
    ip = "<unknown>"
    try:
        #
        conn = httplib.HTTPConnection("hdl.handle.net", 80, strict=False, timeout=_env["timeout_mul"] * timeout_i)
        conn.request( "GET", "/%s?noredirect" % handle )
        ip = get_ip(conn)
        resp = conn.getresponse()
        page = resp.read()
        took = time.time() - took_s
        keys = _env["keys_in_resp"]
        found_key = (0 == len(keys))
        for key in keys:
            if key in page:
                found_key = True
                break
        if not found_key:
            ret = _EXC
            msg = "Invalid page returned, no mff.cuni.cz found on the page [%s]" % page.replace("\n", " ")[:10000]
        elif took >  _env["timeout_soft"] * timeout:
            ret = _EXC_SOFT
            msg = "%d * %d timeout exceeded (soft)" % (_env["timeout_soft"], timeout_i)
        elif took > timeout:
            ret = _WARN
            msg = "%d timeout exceeded (warn)" % timeout_i
        elif resp.status != 200:
            ret = _EXC
            msg = "%s - invalid response" % resp.reason
    except (urllib2.URLError, socket.timeout):
        took = time.time() - took_s
        ret = _EXC
        msg = "%d * %d timeout exceeded (socket timeout)" % (_env["timeout_mul"], timeout_i)
    except Exception as e:
        took = time.time() - took_s
        ret = _EXC
        msg = repr(e)

    if ret != _OK:
        _log("EXC Problematic handle [%s], started at [%s], took [%s], msg [%s], resolved by [%s]" % (
            handle, started_dt, took, msg, ip))
    return ret, msg, took


def check():
    """
        Check the handles
    """
    file_str = _env["input_file"]
    try:
        with open(file_str, mode="rb") as fin:
            urls = fin.readlines()
    except Exception, e:
        return _EXC, "Invalid input file specified [%s] [%s]" % (file_str, repr(e))
    try:
        timeout = float(_env["timeout"])
    except Exception, e:
        return _EXC, "Invalid timeout specified [%s] [%s]" % (_env["timeout"], repr(e))

    status = _OK
    msgs = []
    tooks = []
    for handle in [ x.strip() for x in urls if len(x.strip()) > 0 ]:
        ret, msg, took = check_resolving_handle_timeout( handle, timeout )
        tooks.append(took)
        # just slow
        if ret == _WARN:
            status = _WARN
            msgs.append(msg)
        # failed
        if ret == _EXC:
            return _EXC, "handle [%s] not resolved [%s]" % (handle, msg)
        elif ret == _EXC_SOFT:
            return _EXC_SOFT, "handle [%s] resolved but slowly [%s]" % (handle, msg)

    mean = sum(tooks) / max(1, len(tooks))
    return status, "checked [%s] files, timeout [%ss], mean [%ss], max [%ss], min [%ss] %s" % (
        len(urls), timeout, mean, max(tooks), min(tooks), "".join(msgs))


if __name__ == "__main__":
    took = time.time()
    try:
        # THIS SHOULD BE rewritten with normal command line arguments
        #
        if len(sys.argv) < 4:
            _exit( _EXC, "Not enough parameters", 0 )
        _env["input_file"] = sys.argv[1]
        _env["timeout"] = sys.argv[2]
        _env["log_file"] = sys.argv[3]
        try:
            argc = 4
            if sys.argv[argc] == "nocheck":
                _env["keys_in_resp"] = []
                argc += 1
            # 0: [40,60,0,60],
            # 1: [40,60,0,60],
            # WARN
            if int(sys.argv[argc]):
                warn_val = int(sys.argv[argc])
                _env["NAGIOS_VALS"][0][0] = warn_val
                _env["NAGIOS_VALS"][1][0] = warn_val
                argc += 1
            if int(sys.argv[argc]):
                err_val = int(sys.argv[argc])
                _env["NAGIOS_VALS"][0][1] = err_val
                _env["NAGIOS_VALS"][0][3] = err_val
                _env["NAGIOS_VALS"][1][1] = err_val
                _env["NAGIOS_VALS"][1][3] = err_val
                argc += 1
        except:
            pass

        code, msg = check()
    except Exception, e:
        code = _EXC
        msg = repr(e)
    finally:
        took = time.time() - took

    _exit( code, msg, took )
