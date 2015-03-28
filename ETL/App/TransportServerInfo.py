# encoding: utf-8
import sys, os

sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Controller'))

reload(sys)
sys.setdefaultencoding("cp1252")

import re, math, string
from inspect import stack
from datetime import datetime
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
from Constants import *
from Config import CConfig
from Utility import Utilities
from Database import CDatabase
from TransactionModel import CQuery
from MssqlDBModel import CMssqlDBModel
from MysqlDBModel import CMysqlDBModel
from MongoDBController import CMongodbController
from TransportData import CTransportData
import json
from netaddr import *

ConvertNone2Empty = lambda value_object:"" if value_object is None	else value_object
#**************************************************************
#Class CTransportServerInfo                                   *
#Description: Define transport physical & virtual server info *
#**************************************************************
class CTransportServerInfo(CTransportData):
	def __init__(self):
		super(CTransportServerInfo, self).__init__()
		self.m_oMDRDriver 	  	  = CDatabase(self.m_oConfig.MDRHost, self.m_oConfig.MDRUser, self.m_oConfig.MDRPassword, self.m_oConfig.MDRPort, self.m_oConfig.MDRSource)
		self.m_oMysqldbModel	  = CMysqlDBModel()

	#*****************************************************************
	# Function: GetPhysicalServerInterfaceFromMDR
	# Description: Get Interface Info of server from MDR
	#*****************************************************************
	def GetPhysicalServerInterfaceFromMDR(self):
		try:
			dServerInfo = dict()

			if self.m_oMysqldbModel.Connect(self.m_oMDRDriver):
				strSQL = self.m_oQuery.GetPhysicalServerIntefaceInfoQuery()
				oDataResult = self.m_oMysqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						strServerKey 		= row['server_key']

						dPrivateInterface	= dict()
						dPublicInterface	= dict()

						try:
							dPrivateInterface = json.loads(row['zbx_private_interfaces'])
						except:
							dPrivateInterface = dict()

						try:
							dPublicInterface  = json.loads(row['zbx_public_interfaces'])
						except:
							dPublicInterface	= dict()

						if strServerKey not in dServerInfo.keys():
							dServerInfo[strServerKey] = dict()

						dInterface			  = dServerInfo[strServerKey]
						dInterface['private'] = []
						dInterface['public']  = []

						if len(dPrivateInterface) > 0:
							for dItem in dPrivateInterface:
								dInfo 					= dict()
								dInfo['ip'] 			= dItem['ip']
								dInfo['mac_address']	= dItem['mac']
								dInfo['vlan']			= ""
								if self.CheckInterfaceExists(dInterface['private'], dInfo) is False:
									dInterface['private'].append(dInfo)

						if len(dPublicInterface) > 0:
							for dItem in dPublicInterface:
								dInfo 					= dict()
								dInfo['ip'] 			= dItem['ip']
								dInfo['mac_address']	= dItem['mac']
								dInfo['vlan']			= ""
								if self.CheckInterfaceExists(dInterface['public'], dInfo) is False:
									dInterface['public'].append(dInfo)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return dServerInfo

	#*****************************************************************
	# Function: CheckInterfaceExists
	# Description: Check interface exists or not in array interface
	# Parameter: arrInterface,  dInterfaceInfo
	# Result: True or False
	#*****************************************************************
	def CheckInterfaceExists(self, arrInterface, dInterfaceInfo):
		bResult = False
		try:
			for dInfo in arrInterface:
				if (dInfo["ip"].lower() == dInterfaceInfo["ip"].lower()
				    and dInfo["mac_address"].lower() == dInterfaceInfo["mac_address"].lower()):
					bResult = True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return bResult

	#*****************************************************************
	# Function: GetPhysicalServerInfo
	# Description: Get physical server info from record data in cmdb
	# Parameter: dRecordInfo, oProductCollection
	# Result: Dictionary physical server info
	#*****************************************************************
	def GetPhysicalServerInfo(self, dRecordInfo, oProductCollection, oTechnicalGroupCollection):
		try:
			dServerInfo = dict()
			dServerInfo['code']			= strCode  	 	= ConvertNone2Empty(dRecordInfo['A_HOST_SERIALNUMBER'])
			dServerInfo['asset_code']	= strAssetCode 	= ConvertNone2Empty(dRecordInfo['A_HOST_SYSTEMASSETTAG'])
			dServerInfo['server_name']	= strServerName	= ConvertNone2Empty(dRecordInfo['A_HOST_HOSTNAME'])
			dServerInfo['site']			= strSite		= ConvertNone2Empty(dRecordInfo['A_HOST_SITE'])
			dServerInfo['rack']			= strRack  	 	= ConvertNone2Empty(dRecordInfo['A_HOST_RACK'])
			try:
				iU  = int(dRecordInfo['A_HOST_U'])
			except:
				iU	= 0
			dServerInfo['u']			= iU

			try:
				iBay = int(dRecordInfo['A_HOST_BAY'])
			except:
				iBay = 0

			dServerInfo['bay']			= iBay
			dServerInfo['chassis']		= strChassis  	= ConvertNone2Empty(dRecordInfo['A_HOST_IPCHASSIS'])
			dServerInfo['bucket']		= strBucket  	= ConvertNone2Empty(dRecordInfo['BUDKET'])

			#Set Server Type
			#-1: unknow, 1: virtual, 2: server U, 3: server Chassis
			if iU > 0:
				iServerType = SERVER_U
			elif strChassis != "" and iU == 0:
				iServerType = SERVER_CHASSIS
			else:
				iServerType = UNKNOWN

			dServerInfo['server_type']		= iServerType
			dServerInfo['purpose_use'] 		= strPurposeUse  		= ConvertNone2Empty(dRecordInfo['PURPOSE_USE'])
			dServerInfo['product_alias'] 	= strProductAlias		= ConvertNone2Empty(dRecordInfo['A_HOST_PRODUCT'])
			dServerInfo['product_code'] 	= strProductCode		= ConvertNone2Empty(dRecordInfo['A_HOST_PRODUCT_CODE'])
			ObjectProductId					= self.m_oMongodbController.GetProductIdIdByProductAlias(oProductCollection, strProductAlias)
			dServerInfo['product_id']		= ObjectProductId
			dServerInfo['department_alias'] = strDepartmentAlias  	= ConvertNone2Empty(dRecordInfo['A_HOST_DEPARTMENT'])
			dServerInfo['department_code']  = strDepartmentCode  	= ConvertNone2Empty(dRecordInfo['A_HOST_DEPARTMENT_CODE'])
			dServerInfo['division_alias'] 	= strDivisonAlias	 	= ConvertNone2Empty(dRecordInfo['A_HOST_DIVISION'])
			dServerInfo['server_model'] 	= strServerModel 	 	= ConvertNone2Empty(dRecordInfo['A_HOST_MODEL'])
			dServerInfo['cpu_config'] 		= strCPUConfig  	 	= ConvertNone2Empty(dRecordInfo['A_HOST_NOCPU'])
			dServerInfo['memory_size'] 		= strMemorySize  	 	= ConvertNone2Empty(dRecordInfo['A_HOST_MEMORYSIZE'])
			dServerInfo['ram_config'] 		= strRAMConfig  	 	= ConvertNone2Empty(dRecordInfo['A_HOST_RAMCONFIG'])
			dServerInfo['hdd_size'] 		= strHDDSize  	 		= ConvertNone2Empty(dRecordInfo['A_HOST_HDDSIZE'])
			dServerInfo['hdd_raid'] 		= strHDDRaid  	 		= ConvertNone2Empty(dRecordInfo['A_HOST_RAIDTYPE'])
			dServerInfo['ip_console']		= strIPConsole  	 	= ConvertNone2Empty(dRecordInfo['IP_CONSOLE'])
			dServerInfo['os'] 				= strOS  	 			= ConvertNone2Empty(dRecordInfo['A_HOST_OS'])
			dServerInfo['software_list'] 	= strSoftwareList  		= ConvertNone2Empty(dRecordInfo['A_HOST_SOFTWARELIST'])
			strStatus		  	= ConvertNone2Empty(dRecordInfo['A_HOST_STATUS'])
			strStatus			= strStatus.lower()
			try:
				iStatus			= SERVER_STATUS[strStatus]
			except:
				iStatus			= UNKNOWN
			dServerInfo['status'] 				= iStatus

			strNote  	 		= ConvertNone2Empty(dRecordInfo['A_HOST_NOTE'])
			strNote				= Utilities.ConvertToUTF8(strNote)
			dServerInfo['note'] 				= strNote
			dServerInfo['technical_group_name']	= strTechnicalGroupName	= ConvertNone2Empty(dRecordInfo['A_HOST_OWNER'])
			ObjectTechnicalId					= self.m_oMongodbController.GetTechnicalGroupIdByName(oTechnicalGroupCollection, strTechnicalGroupName)
			dServerInfo['technical_group_id']	= ObjectTechnicalId

			dServerInfo['p_vlan_public'] 		= strPVlanPublic		= ConvertNone2Empty(dRecordInfo['A_HOST_VLAN1'])
			dServerInfo['p_vlan_private']		= strPVlanPrivate		= ConvertNone2Empty(dRecordInfo['A_HOST_VLAN2'])
			dServerInfo['power_status'] 		= UNKNOWN
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return dServerInfo

	#*****************************************************************
	# Function: PushPhysicalServerInfo
	# Description: push data physical server
	#*****************************************************************
	def  PushPhysicalServerInfo(self):
		try:
			oDataResult = None
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oServerCollection  		  = PymongoCollection(self.m_oDatabaseMongodb, CLT_SERVER, False)
				oProductCollection 		  = PymongoCollection(self.m_oDatabaseMongodb, CLT_PRODUCT, False)
				oTechnicalGroupCollection = PymongoCollection(self.m_oDatabaseMongodb, CLT_TECHNICAL_OWNER_GROUP, False)

				if oServerCollection is None:
					strErrorMsg = "Cannot connect to %s " % self.m_oConfig.CMDBv2Host
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
					return 0

				strSQL = self.m_oQuery.GetPhysicalServerQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						dServerInfo = dict()
						dServerInfo = self.GetPhysicalServerInfo(row, oProductCollection, oTechnicalGroupCollection)
						self.m_oMongodbController.SaveServerPhysical(oServerCollection, dServerInfo)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************************************
	# Function: UpdatePhysicalServerInterface
	# Description: update interface of physical server from MDR after
	# mining physical server
	#*****************************************************************
	def  UpdatePhysicalServerInterface(self):
		try:
			dServerInfo 	   = dict()
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			oServerCollection  = PymongoCollection(self.m_oDatabaseMongodb, CLT_SERVER, False)
			if oServerCollection is None:
				strErrorMsg = "Cannot connect to %s " % self.m_oConfig.CMDBv2Host
				Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
				return 0

			dServerInfo = self.GetPhysicalServerInterfaceFromMDR()

			if len(dServerInfo) > 0:
				for strServerKey, dInterfaceInfo in dServerInfo.items():
					oDataResult = oServerCollection.find({'code':strServerKey})

					if Utilities.CheckExistence(oDataResult) is not False:
						oServerCollection.update({'code':strServerKey},
												{'$set': {'private_interface': dInterfaceInfo['private'],
														  'public_interface': dInterfaceInfo['public']}})

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#*****************************************************************
	# Function: GetVirtualServerInfo
	# Description: Get virtual server info from record data in cmdb
	# Parameter: dRecordInfo, oProductCollection
	# Result: Dictionary virtual server info
	#*****************************************************************
	def GetVirtualServerInfo(self, dRecordInfo, oProductCollection, oTechnicalGroupCollection):
		try:
			dServerInfo = dict()
			dServerInfo['vm_center'] 		= strVMCenter			= ConvertNone2Empty(dRecordInfo['VM_CENTER'])
			dServerInfo['vm_id'] 			= strVMId				= ConvertNone2Empty(dRecordInfo['VM_ID'])

			dServerInfo['code']				= strCode 				= '%s_%s' % (strVMId, strVMCenter)
			dServerInfo['asset_code']		= ""
			dServerInfo['server_name']		= strServerName	= ConvertNone2Empty(dRecordInfo['SERVER_NAME'])
			dServerInfo['os_server_name']	= strSite		= ConvertNone2Empty(dRecordInfo['OS_SERVER_NAME'])
			dServerInfo['bucket']			= strBucket  	= ConvertNone2Empty(dRecordInfo['BUCKET'])
			dServerInfo['server_type']		= SERVER_VIRTUAL
			dServerInfo['purpose_use'] 		= strPurposeUse  		= ConvertNone2Empty(dRecordInfo['PURPOSE_USE'])
			dServerInfo['product_alias'] 	= strProductAlias		= ConvertNone2Empty(dRecordInfo['PRODUCT'])
			dServerInfo['product_code'] 	= strProductCode		= ConvertNone2Empty(dRecordInfo['PRODUCT_CODE'])
			ObjectProductId					= self.m_oMongodbController.GetProductIdIdByProductAlias(oProductCollection, strProductAlias)
			dServerInfo['product_id']		= ObjectProductId
			dServerInfo['department_alias'] = strDepartmentAlias  	= ConvertNone2Empty(dRecordInfo['DEPARTMENT'])
			dServerInfo['department_code']  = strDepartmentCode  	= ConvertNone2Empty(dRecordInfo['DEPARTMENT_CODE'])
			dServerInfo['division_alias'] 	= strDivisonAlias	 	= ConvertNone2Empty(dRecordInfo['DIVISION'])
			dServerInfo['cpu_config'] 		= strCPUConfig  	 	= ConvertNone2Empty(dRecordInfo['CPU'])
			dServerInfo['memory_size'] 		= strMemorySize  	 	= ConvertNone2Empty(dRecordInfo['MEMORY'])
			dServerInfo['hdd_size'] 		= strHDDSize  	 		= ConvertNone2Empty(dRecordInfo['HDD'])
			dServerInfo['os'] 				= strOS  	 			= ConvertNone2Empty(dRecordInfo['OS'])
			dServerInfo['status'] 			= INUSED

			#*************************************************************
			#Power Status
			strStatus		  	= ConvertNone2Empty(dRecordInfo['STATUS'])
			strStatus			= strStatus.lower()
			try:
				iStatus			= SERVER_VM_POWERESTATUS[strStatus]
			except:
				iStatus			= UNKNOWN

			dServerInfo['power_status']			= iStatus
			#*************************************************************

			dServerInfo['note'] 				= strNote  	 			= ConvertNone2Empty(dRecordInfo['NOTE'])
			strUpdateTime						= ConvertNone2Empty(dRecordInfo['UPDATE_TIME'])
			iClock								= Utilities.ConvertTimeToClock(strUpdateTime, self.m_oConfig)
			dServerInfo['last_updated']			= iClock
			dServerInfo['vm_key']				= strVMKey				= ConvertNone2Empty(dRecordInfo['VM_KEY'])
			dServerInfo['vid'] 					= strVID				= ConvertNone2Empty(dRecordInfo['VID'])
			dServerInfo['physical_ip'] 			= strPhysicalIP			= ConvertNone2Empty(dRecordInfo['PHYSICAL_SERVER_IP'])
			dServerInfo['physical_SN']			= strPhysicalSN			= ConvertNone2Empty(dRecordInfo['PHYSICAL_SERVER_SN'])
			dServerInfo['vmtool'] 				= strVMTool				= ConvertNone2Empty(dRecordInfo['VMWTOOL'])
			dServerInfo['technical_group_name'] = strTechnicalGroupName	= ConvertNone2Empty(dRecordInfo['TECH_OWNER'])
			ObjectTechnicalId					= self.m_oMongodbController.GetTechnicalGroupIdByName(oTechnicalGroupCollection, strTechnicalGroupName)
			dServerInfo['technical_group_id']	= ObjectTechnicalId

			strIPAddress		= ConvertNone2Empty(dRecordInfo['IP_ADDRESS'])
			strMacAddress		= ConvertNone2Empty(dRecordInfo['MAC_ADDRESS'])
			strVlan				= ConvertNone2Empty(dRecordInfo['VLAN'])

			arrPublicInterface  = []
			arrPrivateInterface = []

			arrPublicInterface, arrPrivateInterface = Utilities.GetInterfaceServerVM(strCode, strIPAddress, strMacAddress, strVlan, self.m_oConfig)
			dServerInfo['public_interface'] 		= arrPublicInterface
			dServerInfo['private_interface'] 		= arrPrivateInterface
			try:
				dServerInfo['deleted']				= dRecordInfo['STATUS_DELETE']
			except:
				dServerInfo['deleted']				= UNKNOWN

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return dServerInfo

	#*****************************************************************
	# Function: PushVirtualServerInfo
	# Description: push data virtual server
	#*****************************************************************
	def  PushVirtualServerInfo(self):
		try:
			oDataResult = None
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oServerCollection  		  = PymongoCollection(self.m_oDatabaseMongodb, CLT_SERVER, False)
				oProductCollection 		  = PymongoCollection(self.m_oDatabaseMongodb, CLT_PRODUCT, False)
				oTechnicalGroupCollection = PymongoCollection(self.m_oDatabaseMongodb, CLT_TECHNICAL_OWNER_GROUP, False)

				if oServerCollection is None:
					strErrorMsg = "Cannot connect to %s " % self.m_oConfig.CMDBv2Host
					Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
					return 0

				strSQL = self.m_oQuery.GetVirtualServerQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						dServerInfo = dict()

						dServerInfo = self.GetVirtualServerInfo(row, oProductCollection, oTechnicalGroupCollection)
						self.m_oMongodbController.SaveServerVM(oServerCollection, dServerInfo)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************
	# Function: MigrationData
	# Description: Main Migration Data
	#*****************************************
	def MigrationData(self, strFunction):
		try:
			if strFunction == PHYSICAL_SERVER_MINING:
				self.PushPhysicalServerInfo()
				Utilities.WriteDataLog("Physical Server info was migrated successfully", INFO, self.m_oConfig)
				self.UpdatePhysicalServerInterface()
				Utilities.WriteDataLog("Interface of physical server was updated successfully", INFO, self.m_oConfig)
			elif strFunction == VIRTUAL_SERVER_MINING:
				self.PushVirtualServerInfo()
				Utilities.WriteDataLog("Virtual Server info was migrated successfully", INFO, self.m_oConfig)
			Utilities.WriteDataLog("=========================================", INFO, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

