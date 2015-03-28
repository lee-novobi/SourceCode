# encoding: utf-8
# Description: implement some common utilities
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))

import re, time
from datetime import datetime, timedelta
from inspect import stack
from netaddr import *
from Constants import *
from subprocess import *

# Class Utilities
# Define some functions interaction with file
class Utilities:
	def __init__(self):
		pass
	@staticmethod
	def WriteErrorLog(strErrorMsg, oConfig):
		strFileLog	= oConfig.ErrorLog
		fnLog		= open(strFileLog, "a")
		try:
			timeAt		= time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
			strMsg		= '[%s][%s][%s]\r\n' % (timeAt, ERROR, strErrorMsg)
			fnLog.write(strMsg)
		except Exception, exc:
			strErrorMsg = 'Error: %s' % str(exc) # give a error message
			sys.stderr.write(strErrorMsg)
		finally:
			fnLog.close()

	@staticmethod
	def WriteDataLog(strInfoMsg, strType, oConfig):
		strFullFileLog = oConfig.DataLog
		fLog = open(strFullFileLog, 'a')
		try:
			timeAt		= time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
			strMsg		= '[%s][%s][%s]\r\n' % (timeAt, strType, strInfoMsg)
			fLog.write(strMsg)
			#sys.stderr.write(strMsg)
		except Exception, exc:
			strErrorMsg = 'Error: %s\n' % str(exc) # give a error message
			sys.stderr.write(strErrorMsg)
		finally:
			fLog.close()


	@staticmethod
	def CheckExistence(oResultSet):
		try:
			dFirstItem = oResultSet[0]
			return dFirstItem
		except Exception, exc:
			return False

	@staticmethod
	def  IsExistsSpecialChars(strValue, strPattern, oConfig):
		try:
			arrElement = re.compile(strPattern, re.M|re.I).findall(strValue)
			if len(arrElement) > 0:
				return True
			return False
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

	@staticmethod
	def TransferDataTrapper2Zabbix(strProcessName, value, oConfig):
		try:
			try:
				value = int(value)
				command = 'curl --insecure --data \"hostname=%s&key=%s_%s_Time&value=%s\" https://%s/zabbix/services/zabbix_trapper.php' %(oConfig.GetHostTrapper(), oConfig.GetLocationTrapper(), strProcessName, value, oConfig.GetZabbixServer())
			except:
				command = 'curl --insecure --data \"hostname=%s&key=%s_%s_Time&value=\"%s\"\" https://%s/zabbix/services/zabbix_trapper.php' %(oConfig.GetHostTrapper(), oConfig.GetLocationTrapper(), strProcessName, value, oConfig.GetZabbixServer())

			#print command
			subprocess.call(command, shell=True)
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)

	@staticmethod
	def ConvertTimeToClock(strTime, oConfig):
		iClock = 0
		try:
			dtTime = datetime.strptime(strTime, '%Y-%m-%d %H:%M:%S')
			iClock = int(dtTime.strftime("%s"))
		except Exception, exc:
			strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
		finally:
			return iClock

	@staticmethod
	def GetInterfaceServerVM(strCode, strAllIPAddress, strAllMacAddress, strAllVlan, oConfig):
		try:
			arrPublicInterface  = []
			arrPrivateInterface = []

			strAllIPAddress 	= re.sub(' ', '', strAllIPAddress)
			strAllMacAddress	= re.sub(' ', '', strAllMacAddress)
			strAllVlan			= re.sub(' ', '', strAllVlan)


			arrInterface  = strAllIPAddress.split('|')
			arrMacAddress = strAllMacAddress.split('|')
			arrVlan		  = strAllVlan.split('|')

			iLenInterface  = len(arrInterface)
			iLenMacAddress = len(arrMacAddress)
			iLenVlan	   = len(arrVlan)

			iMaxLen		   = iLenInterface

			if iLenInterface >= iLenMacAddress:
				iMaxLen = iLenInterface
			elif iLenInterface < iLenMacAddress:
				iMaxLen = iLenMacAddress

			for i in range(0, iMaxLen):
				try:
					strIPList = arrInterface[i]
				except:
					strIPList = ""

				try:
					strMacAddress = arrMacAddress[i]
				except:
					strMacAddress = ""

				if strIPList == "" and strMacAddress == "":
					continue

				if strIPList == "":
					dPrivateInterface 				 = dict()
					dPrivateInterface['ip'] 		 = ""
					dPrivateInterface['mac_address'] = arrMacAddress[i]
					try:
						dPrivateInterface['vlan']	 = arrVlan[i]
					except:
						dPrivateInterface['vlan']		 = "-"
					arrPrivateInterface.append(dPrivateInterface)

				else:
					arrIP = strIPList.split(';')

					for strIpAddress in arrIP:
						dPublicInterface	= dict()
						dPrivateInterface 	= dict()

						if IPAddress(strIpAddress).is_private():
							dPrivateInterface['ip'] 		 = strIpAddress
							dPrivateInterface['mac_address'] = arrMacAddress[i]
							try:
								dPrivateInterface['vlan']		 = arrVlan[i]
							except:
								dPrivateInterface['vlan']		 = "-"
						else:
							dPublicInterface['ip'] 		 	= strIpAddress
							dPublicInterface['mac_address'] = arrMacAddress[i]
							try:
								dPublicInterface['vlan']	= arrVlan[i]
							except:
								dPublicInterface['vlan']	= "-"

						if len(dPublicInterface) > 0:
							arrPublicInterface.append(dPublicInterface)
						if len(dPrivateInterface) > 0:
							arrPrivateInterface.append(dPrivateInterface)

		except Exception, exc:
			strDataInfo = '[vid:%s][ip:%s][mac:%s][vlan:%s]' % (strCode, strAllIPAddress, strAllMacAddress, strAllVlan)
			strErrorMsg = '%s - %s Error: %s - Line: %s' % (strDataInfo, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, oConfig)
		finally:
			return arrPublicInterface, arrPrivateInterface

	@staticmethod
	def ConvertToUTF8(strValue):
		code = '''<?php
					$strValue = "''' + strValue + '''";
				  	echo $strValue;
				?>
				'''
		# open process
		p = Popen(['php'], stdout=PIPE, stdin=PIPE, stderr=STDOUT, close_fds=True)

		# read output
		o = p.communicate(code)[0]

		# kill process
		try:
			os.kill(p.pid, signal.SIGTERM)
		except:
		    pass

		  # return
		return o