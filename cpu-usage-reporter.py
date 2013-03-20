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
availableMemorySum = 0
usedMemorySum = 0
while(True):
    cpuUsage = psutil.cpu_percent()
    usageSum += cpuUsage
    availableMemorySum += psutil.avail_phymem() + psutil.cached_phymem()
    usedMemorySum += psutil.used_phymem()
    if samples > 11:
        if not len(argv) > 1 and os.path.exists(configPath):
            loadUserNameFromFile()
        averageUsage = float(usageSum) / float(samples)
        averageAvailableMemory = float(availableMemorySum) / float(samples)
        averageUsedMemory = float(usedMemorySum) / float(samples)
        print("Pushing usage to server", averageUsage, averageUsedMemory, averageAvailableMemory)
        try:
            url = baseUrl + "/wp-content/plugins/cpu-reporter/submit.php?user=" + username  \
                        + "&usage=" + str(averageUsage) \
                        + "&available_memory=" + str(averageAvailableMemory) \
                        + "&used_memory=" + str(averageUsedMemory)
            response = urllib2.urlopen(url)
            runData = json.load(response)
        except KeyboardInterrupt:
            raise KeyboardInterrupt
        except:
            print("Something bad happened. Don't care...")
        samples = 1
        usageSum = 0
        availableMemorySum = 0
        usedMemorySum = 0
#    print(cpuUsage, psutil.used_phymem() / 1e6, psutil.avail_phymem() / 1e6)
    time.sleep(1)
    samples += 1
