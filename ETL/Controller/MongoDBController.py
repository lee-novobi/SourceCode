import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))

import math, re
from datetime import datetime
from mongokit import Connection
from pymongo.collection import Collection as PymongoCollection
from pymongo.database import Database as PymongoDatabase
from bson.code import Code
from bson.objectid import ObjectId
from inspect import stack
from Config import CConfig
from MongoDBModel import *
from Utility import Utilities
from Constants import *

#**************************************************************
#Class CMongodbController                                     *
#Description: Define functions when implement with mongodb    *
#**************************************************************
class CMongodbController:
	def __init__(self):
		self.m_oConfig = CConfig()
		try:
			self.m_oConnectorMongodb		= Connection(self.m_oConfig.CMDBv2Uri, self.m_oConfig.CMDBv2Port)
			self.m_oDatabaseMongodb			= PymongoDatabase(self.m_oConnectorMongodb, self.m_oConfig.CMDBv2Source)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#**************************************************************
	# Function: GetChangesByName
	# Description: Get changes info by name to get collection name
	# Parameter: string name
	# Result: Dictionary
	#**************************************************************
	def  GetChangesByName(self, strName):
		dResult = dict()
		try:
			oChangesCollection = PymongoCollection(self.m_oDatabaseMongodb, "changes", False)
			dResult = oChangesCollection.find({'name': strName}, {'active':1, 'passive':1, '_id':0})[0]
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return dResult;

	#************************************************************************
	# Function: SwitchChangesActive
	# Description: Switch active collection in document of changes collection
	# Parameter: name collection, name active, name passive
	#************************************************************************
	def  SwitchChangesActive(self, strName, strActive, strPassive):
		bResult = False;

		try:
			oChangesCollection = PymongoCollection(self.m_oDatabaseMongodb, "changes", False)
			if oChangesCollection.find({'name': strName}).count() == 1:
				oChangesCollection.update({ 'name': strName },
										  { '$set': {'active': strPassive, 'passive': strActive} })
				bResult = True
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return bResult

	#************************************************************************
	# Function: GetActiveCollection
	# Description: Get active collection by name
	# Parameter: string name collection
	# Result: Collection
	#************************************************************************
	def	 GetActiveCollection(self, strCollection):
		try:
			oActivePyCollection = None
			dChangesInfo = self.GetChangesByName(strCollection)
			strActive = dChangesInfo['active']
			oActivePyCollection = PymongoCollection(self.m_oDatabaseMongodb, strActive, False)
			return oActivePyCollection
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.oConfig)
			return None

	#***************************************************************
	# Function: SaveDivision
	# Description: Save division table info
	# Parameter: division collection, data dictionary
	#***************************************************************
	def  SaveDivision(self, oDivisionCollection, dDivisionInfo):
		try:
			oDataResult = oDivisionCollection.find({'alias': dDivisionInfo['alias']})

			if Utilities.CheckExistence(oDataResult) is False:
				oDivisionObject				= CDivision(oDivisionCollection)
				oDivisionObject['code']		= dDivisionInfo['code']
				oDivisionObject['alias']	= dDivisionInfo['alias']
				oDivisionObject['hr_id']	= dDivisionInfo['hr_id']
				oDivisionObject['status']	= int(dDivisionInfo['status'])
				oDivisionObject['deleted']	= int(dDivisionInfo['deleted'])
				oDivisionObject.save()
			else:
				oDivisionCollection.update({'alias': dDivisionInfo['alias']},
										    {'$set': dDivisionInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#****************************************************************
	# Function: GetDivisionIdIdByDivsionAlias
	# Description: Get objectId of division by alias
	# Parameter: division collection, string alias
	# Result: ObjectId
	#****************************************************************
	def GetDivisionIdIdByDivsionAlias(self, oDivisionCtl, strAlias):
		try:
			ObjectIdResult = None;
			oDataResult = oDivisionCtl.find({'alias': strAlias})

			if Utilities.CheckExistence(oDataResult) is not False:
				ObjectIdResult = oDataResult[0]['_id']

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return ObjectIdResult

	#****************************************************************
	# Function: GetProductIdIdByProductAlias
	# Description: Get objectId of product by alias
	# Parameter: product collection, string alias
	# Result: ObjectId
	#****************************************************************
	def GetProductIdIdByProductAlias(self, oProductCollection, strAlias):
		try:
			ObjectIdResult = None;
			oDataResult = oProductCollection.find({'alias': strAlias})

			if Utilities.CheckExistence(oDataResult) is not False:
				ObjectIdResult = oDataResult[0]['_id']

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return ObjectIdResult

	#****************************************************************
	# Function: GetTechnicalGroupIdByName
	# Description: Get objectId of technical group by name
	# Parameter: technical group collection, string name
	# Result: ObjectId
	#****************************************************************
	def GetTechnicalGroupIdByName(self, oTechnicalGroupCollection, strName):
		try:
			ObjectIdResult = None;
			oDataResult = oTechnicalGroupCollection.find({'name': strName})

			if Utilities.CheckExistence(oDataResult) is not False:
				ObjectIdResult = oDataResult[0]['_id']

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return ObjectIdResult

	#****************************************************************
	# Function: GetListProductIdIdByListProductAlias
	# Description: Get list objectId of product by list alias
	# Parameter: product collection, string alias
	# Result: array ObjectId
	#****************************************************************
	def GetListProductIdIdByListProductAlias(self, oProductCollection, arrProductAlias):
		try:
			arrProductId = []
			oDataResult = oProductCollection.find({'alias': {'$in': arrProductAlias}})

			if Utilities.CheckExistence(oDataResult) is not False:
				for dProductInfo in oDataResult:
					ObjectIdResult = dProductInfo['_id']
					arrProductId.append(ObjectIdResult)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return arrProductId

	#***************************************************************
	# Function: SaveDepartment
	# Description: Save department table info
	# Parameter: department collection, data dictionary
	#***************************************************************
	def  SaveDepartment(self, oDepartmentCollection, dDepartmentInfo):
		try:
			oDataResult = oDepartmentCollection.find({'alias': dDepartmentInfo['alias']})

			if Utilities.CheckExistence(oDataResult) is False:
				oDepartmentObject					= CDepartment(oDepartmentCollection)
				oDepartmentObject['code']			= dDepartmentInfo['code']
				oDepartmentObject['alias']			= dDepartmentInfo['alias']
				oDepartmentObject['division_id'] 	= dDepartmentInfo['division_id']
				oDepartmentObject['division_alias']	= dDepartmentInfo['division_alias']
				oDepartmentObject['status']			= int(dDepartmentInfo['status'])
				oDepartmentObject['hr_id']			= dDepartmentInfo['hr_id']
				oDepartmentObject['deleted']		= int(dDepartmentInfo['deleted'])
				oDepartmentObject.save()
			else:
				oDepartmentCollection.update({'alias': dDepartmentInfo['alias']},
										    {'$set': dDepartmentInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#****************************************************************
	# Function: GetDeparmentInfoByDepartmentAlias
	# Description: Get Department info (as department_id, department_alias,
	# division_id, division_alias) by department alias
	# Parameter: department collection, string alias
	# Result: Dictionary Info
	#****************************************************************
	def GetDeparmentInfoByDepartmentAlias(self, oDepartmentCollection, strAlias):
		try:
			dDepartmentInfo = dict();
			oDataResult = oDepartmentCollection.find({'alias': strAlias})

			if Utilities.CheckExistence(oDataResult) is not False:
				dDepartmentInfo['department_id'] 	= oDataResult[0]['_id']
				dDepartmentInfo['department_alias'] = oDataResult[0]['alias']
				dDepartmentInfo['department_code']  = oDataResult[0]['code']
				dDepartmentInfo['division_id'] 		= oDataResult[0]['division_id']
				dDepartmentInfo['division_alias']	= oDataResult[0]['division_alias']

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			return dDepartmentInfo

	#***************************************************************
	# Function: SaveProduct
	# Description: Save product table info
	# Parameter: product collection, data dictionary
	#***************************************************************
	def  SaveProduct(self, oProductCollection, dProductInfo):
		try:
			oDataResult = oProductCollection.find({'code': dProductInfo['code'], 'alias': dProductInfo['alias']})

			if Utilities.CheckExistence(oDataResult) is False:
				oProductObject						= CProduct(oProductCollection)
				oProductObject['code']				= dProductInfo['code']
				oProductObject['alias']				= dProductInfo['alias']
				oProductObject['department_id'] 	= dProductInfo['department_id']
				oProductObject['department_alias']	= dProductInfo['department_alias']
				oProductObject['department_code']	= dProductInfo['department_code']
				oProductObject['division_id'] 		= dProductInfo['division_id']
				oProductObject['division_alias']	= dProductInfo['division_alias']
				oProductObject['status']			= int(dProductInfo['status'])
				oProductObject['type']				= dProductInfo['type']
				oProductObject['deleted']			= int(dProductInfo['deleted'])
				oProductObject.save()
			else:
				oProductCollection.update({'code': dProductInfo['code'], 'alias': dProductInfo['alias']},
										    {'$set': dProductInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#***************************************************************
	# Function: SaveTechnicalOwnerGroup
	# Description: Save technical owner group table info
	# Parameter: technical owner group  collection, data dictionary
	#***************************************************************
	def  SaveTechnicalOwnerGroup(self, oTechnicalOwnerCollection, dTechnicalOwnerGroupInfo):
		try:
			oDataResult = oTechnicalOwnerCollection.find({'name': dTechnicalOwnerGroupInfo['name']})

			if Utilities.CheckExistence(oDataResult) is False:
				oTechnicalOwnerGroupObject					= CTechnicalOwnerGroup(oTechnicalOwnerCollection)
				oTechnicalOwnerGroupObject['name']			= dTechnicalOwnerGroupInfo['name']
				oTechnicalOwnerGroupObject['description']	= dTechnicalOwnerGroupInfo['description']
				oTechnicalOwnerGroupObject['description']	= dTechnicalOwnerGroupInfo['email_list']
				oTechnicalOwnerGroupObject['product_id'] 	= dTechnicalOwnerGroupInfo['product_id']
				oTechnicalOwnerGroupObject['deleted'] 		= dTechnicalOwnerGroupInfo['deleted']
				oTechnicalOwnerGroupObject.save()
			else:
				oTechnicalOwnerCollection.update({'name': dTechnicalOwnerGroupInfo['name']},
										    {'$set': dTechnicalOwnerGroupInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#***************************************************************
	# Function: SaveServerPhysical
	# Description: Save server collection info
	# Parameter: product collection, data dictionary
	#***************************************************************
	def  SaveServerPhysical(self, oServerCollection, dServerInfo):
		try:
			oDataResult = oServerCollection.find({'code': dServerInfo['code']})

			if Utilities.CheckExistence(oDataResult) is False:
				oServerObject						= 	CServer(oServerCollection)
				oServerObject['code']				=	dServerInfo['code']
				oServerObject['asset_code']			=	dServerInfo['asset_code']
				oServerObject['server_name']		=	dServerInfo['server_name']
				oServerObject['site']				=	dServerInfo['site']
				oServerObject['rack']				=	dServerInfo['rack']
				oServerObject['u']					=	int(dServerInfo['u'])
				oServerObject['bay']				=	int(dServerInfo['bay'])
				oServerObject['chassis']			=	dServerInfo['chassis']
				oServerObject['bucket']				=	dServerInfo['bucket']
				oServerObject['server_type']		=	int(dServerInfo['server_type'])
				oServerObject['purpose_use']		=	dServerInfo['purpose_use']
				oServerObject['product_code']		=	dServerInfo['product_code']
				oServerObject['product_alias']		=	dServerInfo['product_alias']
				oServerObject['product_id']			=	dServerInfo['product_id']
				oServerObject['department_alias']	=	dServerInfo['department_alias']
				oServerObject['department_code']	=	dServerInfo['department_code']
				oServerObject['division_alias']		=	dServerInfo['division_alias']
				oServerObject['server_model']		=	dServerInfo['server_model']
				oServerObject['cpu_config']			=	dServerInfo['cpu_config']
				oServerObject['memory_size']		=	dServerInfo['memory_size']
				oServerObject['ram_config']			=	dServerInfo['ram_config']
				oServerObject['hdd_size']			=	dServerInfo['hdd_size']
				oServerObject['hdd_raid']			=	dServerInfo['hdd_raid']
				oServerObject['ip_console']			=	dServerInfo['ip_console']
				oServerObject['os']					=	dServerInfo['os']
				oServerObject['software_list']		=	dServerInfo['software_list']
				oServerObject['status']				=	int(dServerInfo['status'])
				oServerObject['note']				=	dServerInfo['note']
				oServerObject['p_vlan_public']		=	dServerInfo['p_vlan_public']
				oServerObject['p_vlan_private']		=	dServerInfo['p_vlan_private']
				oServerObject['power_status']		=	dServerInfo['power_status']
				oServerObject['technical_group_id']	=	dServerInfo['technical_group_id']
				oServerObject['technical_group_name']	=	dServerInfo['technical_group_name']
				try:
					oServerObject.save()
				except:
					#strServerInfo = '; '.join('{}:{}'.format(key, val) for key, val in dServerInfo.items())
					Utilities.WriteErrorLog(dServerInfo, self.m_oConfig)
					oServerObject['note'] = ""
					oServerObject.save()
			else:
				try:
					oServerCollection.update({'code': dServerInfo['code']},
												{'$set': dServerInfo})
				except:
					#strServerInfo = '; '.join('{}:{}'.format(key, val) for key, val in dServerInfo.items())
					Utilities.WriteErrorLog(dServerInfo, self.m_oConfig)
					dServerInfo['note'] = ""
					oServerCollection.update({'code': dServerInfo['code']},
												{'$set': dServerInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#***************************************************************
	# Function: SaveServerVM
	# Description: Save server collection info
	# Parameter: product collection, data dictionary
	#***************************************************************
	def  SaveServerVM(self, oServerCollection, dServerInfo):
		try:
			oDataResult = oServerCollection.find({'code': dServerInfo['code'], 'vm_id':dServerInfo['vm_id'],
												  'vm_center':dServerInfo['vm_center']})

			if Utilities.CheckExistence(oDataResult) is False:
				oServerObject						= 	CServer(oServerCollection)
				oServerObject['code']				=	dServerInfo['code']
				oServerObject['server_name']		=	dServerInfo['server_name']
				oServerObject['os_server_name']		=	dServerInfo['os_server_name']
				oServerObject['bucket']				=	dServerInfo['bucket']
				oServerObject['server_type']		=	int(dServerInfo['server_type'])
				oServerObject['purpose_use']		=	dServerInfo['purpose_use']
				oServerObject['product_code']		=	dServerInfo['product_code']
				oServerObject['product_alias']		=	dServerInfo['product_alias']
				oServerObject['product_id']			=	dServerInfo['product_id']
				oServerObject['department_alias']	=	dServerInfo['department_alias']
				oServerObject['department_code']	=	dServerInfo['department_code']
				oServerObject['division_alias']		=	dServerInfo['division_alias']
				oServerObject['cpu_config']			=	dServerInfo['cpu_config']
				oServerObject['memory_size']		=	dServerInfo['memory_size']
				oServerObject['hdd_size']			=	dServerInfo['hdd_size']
				oServerObject['os']					=	dServerInfo['os']
				oServerObject['status']				=	int(dServerInfo['status'])
				oServerObject['note']				=	dServerInfo['note']
				oServerObject['last_updated']		=	int(dServerInfo['last_updated'])
				oServerObject['vm_center']			=	dServerInfo['vm_center']
				oServerObject['vm_key']				=	dServerInfo['vm_key']
				oServerObject['vm_id']				=	dServerInfo['vm_id']
				oServerObject['vid']				=	dServerInfo['vid']
				oServerObject['note']				=	dServerInfo['note']
				oServerObject['physical_ip']		=	dServerInfo['physical_ip']
				oServerObject['physical_SN']		=	dServerInfo['physical_SN']
				oServerObject['vmtool']				=	dServerInfo['vmtool']
				oServerObject['public_interface']	= 	dServerInfo['public_interface']
				oServerObject['private_interface']	= 	dServerInfo['private_interface']
				oServerObject['technical_group_id']	=	dServerInfo['technical_group_id']
				oServerObject['technical_group_name']	=	dServerInfo['technical_group_name']
				oServerObject['deleted']			= dServerInfo['deleted']
				try:
					oServerObject.save()
				except:
					#strServerInfo = '; '.join('{}:{}'.format(key, val) for key, val in dServerInfo.items())
					Utilities.WriteErrorLog(dServerInfo, self.m_oConfig)
					oServerObject['note'] = ""
					oServerObject.save()
			else:
				try:
					oServerCollection.update({'code': dServerInfo['code'], 'vm_id':dServerInfo['vm_id'],'vm_center':dServerInfo['vm_center']},
												{'$set': dServerInfo})
				except:
					#strServerInfo = '; '.join('{}:{}'.format(key, val) for key, val in dServerInfo.items())
					Utilities.WriteErrorLog(dServerInfo, self.m_oConfig)
					dServerInfo['note'] = ""
					oServerCollection.update({'code': dServerInfo['code']},
												{'$set': dServerInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#***************************************************************
	# Function: SaveUserInfo
	# Description: Save User Info
	# Parameter: user collection, data dictionary
	#***************************************************************
	def  SaveUserInfo(self, oUserCollection, dUserInfo):
		try:
			oDataResult = oUserCollection.find({'username': dUserInfo['username']})

			if Utilities.CheckExistence(oDataResult) is False:
				oUserObject				  = CUser(oUserCollection)
				oUserObject['username']	  = dUserInfo['username']
				oUserObject['full_name']  = dUserInfo['full_name']
				oUserObject['department'] = dUserInfo['department']
				try:
					oUserObject.save()
				except:
					#strUserInfo = '; '.join('{}:{}'.format(key, val) for key, val in dUserInfo.items())
					Utilities.WriteErrorLog(dUserInfo, self.m_oConfig)
					oUserObject['full_name'] = ""
					oUserObject.save()
			else:
				try:
					oUserCollection.update({'username': dUserInfo['username']},
													{'$set': dUserInfo})
				except:
					#strUserInfo = '; '.join('{}:{}'.format(key, val) for key, val in dUserInfo.items())
					Utilities.WriteErrorLog(dUserInfo, self.m_oConfig)
					dUserInfo['full_name'] = ""
					oUserCollection.update({'username': dUserInfo['username']},
													{'$set': dUserInfo})
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
