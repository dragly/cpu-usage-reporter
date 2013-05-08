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
from xlib import XEvents
from sys import argv

# UPDATE THESE VARIABLES
baseUrl = "http://comp-phys.net"
# END UPDATE VARIABLES

def loadUserNameFromFile():
    global username
    configParser = ConfigParser.ConfigParser()
    configParser.read(configPath)
    if configParser.has_section("General"):
        if configParser.has_option("General", "username"):
            username = configParser.get("General", "username")
            print("Updating username to", username)

def getEventCount():
    while not events.listening():
        # Wait for init
        time.sleep(1)
    try:
        clicks_past_period = 0
        while events.listening():
            evt = events.next_event()
            if not evt:
                return clicks_past_period
            
            if evt.type != "EV_KEY" or evt.value != 1: # Only count key down, not up.
                continue

            clicks_past_period+=1
    except:
        print "Caught exception, you are a bad boy"

    return 0

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
#events = XEvents()
#events.start()
availableMemorySum = 0
usedMemorySum = 0
while(True):
    cpuUsage = psutil.cpu_percent()
    usageSum += cpuUsage
    availableMemorySum += psutil.avail_phymem() + psutil.cached_phymem()
    usedMemorySum += psutil.used_phymem()
    if samples > 11:
        #eventCount = getEventCount()
        #print "Events: "+str(eventCount)
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
#                        + "&is_active=" + str(eventCount)
            response = urllib2.urlopen(url)
            runData = json.load(response)
        except KeyboardInterrupt:
 #           events.stop_listening()
            raise KeyboardInterrupt
        except:
            print("Something bad happened. Don't care...")
        samples = 1
        usageSum = 0
        availableMemorySum = 0
        usedMemorySum = 0
    time.sleep(10)
    samples += 1
