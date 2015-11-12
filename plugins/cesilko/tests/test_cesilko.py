#!/usr/bin/python
# coding=utf-8
import unittest
import sys
import time


def send_form(url, payload):
    import requests
    r = requests.post(url, payload, timeout=5.0)
    return r


def magic_lines(r, magic):
    lines = r.content.splitlines()
    interesting_lines = [ x.strip() for x in lines if magic in x and "required" not in x ]
    return interesting_lines if len(interesting_lines) > 0 else [ "" ]


class cesilko_web_app_test(unittest.TestCase):
    """
        Simple web service runner.
    """
    url_to_test = "http://lindat.mff.cuni.cz/services/cesilko/demo.php"
    magic = "jmANCHOR"
    default_return_encoding = "utf-8"

    translations = {
        "ascii": [
            ( u"mohl", u"mohol"),
        ],
        "utf-8": [
            ( u"případu", u"prípadu"),
        ],
        "iso-8859-2": [
            ( u"případu", u"prípadu"),
        ],
    }

    @staticmethod
    def word(type_idx, lang, idx=0):
        lang_id = 0 if lang == "cz" else 1
        word = cesilko_web_app_test.translations[type_idx][idx][lang_id]
        return unicode(cesilko_web_app_test.magic) + u" " + word

    def _test_result( self, word ):
        r = send_form(
            cesilko_web_app_test.url_to_test,
            { u"text": word }
        )
        interesting_line = magic_lines( r, cesilko_web_app_test.magic )[0]
        self.assertTrue(len(interesting_line) > 0, "Received no translation!")
        return interesting_line

    #
    #

    def test_ascii(self):
        """ Test sending ascii """
        enc = "ascii"
        interesting_line = self._test_result( str(self.word(enc, "cz")) )
        self.assertTrue( self.word(enc, "sk") in interesting_line.decode(
            cesilko_web_app_test.default_return_encoding) )

    def test_utf8(self):
        """ Test sending utf-8 """
        enc = "utf-8"
        interesting_line = self._test_result( self.word(enc, "cz").encode(enc) )
        self.assertTrue( self.word(enc, "sk") in interesting_line.decode(
            cesilko_web_app_test.default_return_encoding) )

    #def test_iso88592(self):
    #    """ Test sending iso """
    #    enc = "iso-8859-2"
    #    print self.word(enc, "cz")
    #    print self.word(enc, "cz").encode(enc)
    #    interesting_line = self._test_result( self.word(enc, "cz").encode(enc) )
    #    self.assertTrue( self.word(enc, "sk") in interesting_line.decode(
    #        cesilko_web_app_test.default_return_encoding) )


#noinspection PyMethodMayBeStatic
class __fake_stdout( object ):
        def write(self, text):
            """ File like write method. """
            pass

        def flush(self):
            """ File like flush method. """
            pass


_EXC = 2
_WARN = 1
_OK = 0


def _nagios_exit( code, msg_str, time_d ):
    warn_time = 12
    crit_time = 24
    min_time = 0
    max_time = 30
    if code == _EXC:
        warn_time = 0
        crit_time = 1
        min_time = 0
        max_time = 1

    msg_whole = "%s total time [%ss] with ret code [%s]|time=%ss;%s;%s;%s;%s\n" % (
        msg_str, time_d, code, int(time_d), warn_time, crit_time, min_time, max_time)
    print( msg_whole )
    sys.exit(code)


if __name__ == '__main__':
    took = time.time()
    nagios = True if len(sys.argv) > 1 and sys.argv[1] == "nagios" \
        else False
    stream = __fake_stdout() if nagios else sys.stderr
    try:
        suite = unittest.TestLoader().loadTestsFromTestCase(cesilko_web_app_test)
        tr = unittest.TextTestRunner(stream=stream, verbosity=2).run(suite)
    finally:
        took = time.time() - took

    if nagios:
        code = _OK if len(tr.failures) == 0 else _EXC
        msg = u"failed %s/%s" % (len(tr.failures), tr.testsRun)
        if 0 < len(tr.failures):
            msg += u" " + u",".join( [ repr(x[0].id()) for x in tr.failures] )
        _nagios_exit( code, msg, took )
