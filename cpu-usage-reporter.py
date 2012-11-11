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
import random
import os
import ConfigParser
from sys import argv

# UPDATE THESE VARIABLES
baseUrl = "http://compphys.dragly.org"
# END UPDATE VARIABLES

def loadUserNameFromFile():
    global username
    configParser = ConfigParser.ConfigParser()
    configParser.read(configPath)
    if configParser.has_section("General"):
        if configParser.has_option("General", "username"):
            username = configParser.get("General", "username")
            print("Updating username to", username)
    

configPath = "/etc/cpu-usage-reporter.conf"
username = "unnamed" + ("%.0f" % (random.random() * 100))


# If a username is supplied by argument
if len(argv) > 1:
    username = argv[1]
# If a config file has been set up
elif os.path.exists(configPath):
    loadUserNameFromFile()

print("Using username " + username)

samples = 1
usageSum = 0
while(True):
    cpuUsage = psutil.cpu_percent()
    usageSum += cpuUsage
    if samples > 11:
        if os.path.exists(configPath):
            loadUserNameFromFile()
        averageUsage = float(usageSum) / float(samples)
        print("Pushing usage to server", averageUsage)
        try:
            runData = json.load(urllib2.urlopen(baseUrl + "/wp-content/plugins/cpu-reporter/submit.php?user=" + username + "&usage=" + str(averageUsage)))
        except KeyboardInterrupt:
            raise KeyboardInterrupt
        except:
            print("Something bad happened. Don't care...")
        samples = 1
        usageSum = 0
    print(cpuUsage)
    time.sleep(1)
    samples += 1
