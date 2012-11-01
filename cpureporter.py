# -*- coding: utf-8 -*-
"""
Created on Fri Sep 21 18:39:38 2012

@author: svenni
"""

import psutil
import json
import urllib2
import time
from sys import argv

# UPDATE THESE VARIABLES
baseUrl = "http://compphys.dragly.org"
# END UPDATE VARIABLES

username = argv[1]

while(True):
    cpuUsage = psutil.cpu_percent()
    try:
        runData = json.load(urllib2.urlopen(baseUrl + "/wp-content/plugins/cpu-reporter/submit.php?user=" + username + "&usage=" + str(cpuUsage)))
    except KeyboardInterrupt:
        raise KeyboardInterrupt
    except:
        print "Something bad happened. Don't care..."
    print cpuUsage
    time.sleep(60)
