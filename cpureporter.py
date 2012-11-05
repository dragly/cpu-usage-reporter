#!/usr/bin/python
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

samples = 1
usageSum = 0
while(True):
    cpuUsage = psutil.cpu_percent()
    usageSum += cpuUsage
    if samples > 11:
        averageUsage = float(usageSum) / float(samples)
        print("Pushing usage to server", averageUsage)
        try:    
            runData = json.load(urllib2.urlopen(baseUrl + "/wp-content/plugins/cpu-reporter/submit.php?user=" + username + "&usage=" + str(averageUsage)))
        except KeyboardInterrupt:
            raise KeyboardInterrupt
        except:
            print("Something bad happened. Don't care...")
        samples = 1
    print(cpuUsage)
    time.sleep(5)
    samples += 1
