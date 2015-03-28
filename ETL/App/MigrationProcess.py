# Description: main processing file for migration data
# encoding: utf-8
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

import time
from inspect import stack
from Config import CConfig
from TransportData import CTransportData
from TransportServerInfo import CTransportServerInfo
from TransportUserInfo import CTransportUserInfo
from Utility import Utilities
from Constants import *

#Class MigrationThread proccess migration data
class CMigrationThread(object):
	def __init__(self):
		# Create config object
		self._oConfig = CConfig()
		# Create Transport Data Objects
		self._oTransportData 		= CTransportData()
		self._oTransportServerInfo 	= CTransportServerInfo()
		self._oTransportUserInfo 	= CTransportUserInfo();

	#Run threading
	def Migration(self, strFunction):
		try:
			if (strFunction == GENERAL_MINING):
				self._oTransportData.MigrationData()
			elif (strFunction == PHYSICAL_SERVER_MINING
				  or strFunction == VIRTUAL_SERVER_MINING):
				self._oTransportServerInfo.MigrationData(strFunction)
			elif (strFunction == USER_MINING):
				self._oTransportUserInfo.MigrationData()

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)

if __name__ == '__main__':
	nStartTime = time.time()
	oConfig    = CConfig()
	try:
		oMigrationThread = CMigrationThread()
		if len(sys.argv) == 2:
			strFunction = sys.argv[1]
			oMigrationThread.Migration(strFunction)
	except Exception, exc:
		strErrorMsg = '%s Error: %s - Line: %s' % (str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
		Utilities.WriteErrorLog(strErrorMsg, oConfig)

	nEndTime = time.time()
	nDuration = nEndTime - nStartTime
	print 'Duration:%s' % nDuration
	#Utilities.TransferDataTrapper2Zabbix("migration_cmdb", nDuration, oConfig)
