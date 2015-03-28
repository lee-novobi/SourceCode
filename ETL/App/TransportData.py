# encoding: utf-8
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Model'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Controller'))

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
from MongoDBController import CMongodbController

ConvertNone2Empty = lambda value_object:"" if value_object is None	else value_object
#**************************************************************
#Class CTransportData                                         *
#Description: Define transport functions in the mining data   *
#**************************************************************
class CTransportData(object):
	def __init__(self):
		# Create config object
		self.m_oConfig = CConfig()
		try:
			# Create database provider
			self.m_oCMDBDriver 	  	  = CDatabase(self.m_oConfig.CMDBHost, self.m_oConfig.CMDBUser, self.m_oConfig.CMDBPassword, self.m_oConfig.CMDBPort, self.m_oConfig.CMDBSource)
			self.m_oMssqldbModel	  = CMssqlDBModel()

			# Create mongodb controller object
			self.m_oMongodbController = CMongodbController()

			# Create Query model object
			self.m_oQuery 			  = CQuery()
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

	#*****************************************************************
	# Function: IsConnectedToCMDBv2
	# Description: Check connection to CMDBv2
	# Result: True or False
	#*****************************************************************
	def IsConnectedToCMDBv2(self):
		try:
			# Create connector mongokit object
			self.m_oConnectorMongodb  = Connection(self.m_oConfig.CMDBv2Uri, self.m_oConfig.CMDBv2Port)
			self.m_oDatabaseMongodb	  = PymongoDatabase(self.m_oConnectorMongodb, self.m_oConfig.CMDBv2Source)
			return True
		except:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
			return False

	#*****************************************************************
	# Function: PushDivisionInfo
	# Description: push data division info from CMDB
	#*****************************************************************
	def  PushDivisionInfo(self):
		try:
			oDataResult = None
			#arrDivisionAlias = []

			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oDivisionCollection = PymongoCollection(self.m_oDatabaseMongodb, CLT_DIVISION, False)
				strSQL = self.m_oQuery.GetDivisionQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)
				if oDataResult is not None:
					for row in oDataResult:
						dDivisionInfo = dict()
						strCode  = ConvertNone2Empty(row['DIVISION_CODE'])
						strAlias = ConvertNone2Empty(row['DIVISION_ALIAS'])
						strHrId	 = ConvertNone2Empty(str(row['DIVISION_HR_ID']))
						iStatus  = ACTIVE

						dDivisionInfo['code'] 	= strCode
						dDivisionInfo['alias'] 	= strAlias
						dDivisionInfo['hr_id'] 	= strHrId
						dDivisionInfo['status'] = iStatus
						dDivisionInfo['deleted']= 0

						self.m_oMongodbController.SaveDivision(oDivisionCollection, dDivisionInfo)

						#if strAlias not in arrDivisionAlias:
						#	arrDivisionAlias.append(strAlias)

					#if len(arrDivisionAlias) > 0:
					#	oDivisionCollection.update({'alias':{'$nin':arrDivisionAlias}},
					#							   {'$set': {'deleted': 1}},
					#							   	upsert=False,
					#								multi=True)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************************************
	# Function: PushDepartmentInfo
	# Description: push data department info from CMDB
	#*****************************************************************
	def  PushDepartmentInfo(self):
		try:
			oDataResult = None
			#arrDeptAlias = []
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oDepartmentClt  = PymongoCollection(self.m_oDatabaseMongodb, CLT_DEPARTMENT, False)
				oDivisionClt	= PymongoCollection(self.m_oDatabaseMongodb, CLT_DIVISION, False)

				strSQL = self.m_oQuery.GetDepartmentQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						dDeptInfo  = dict()
						strCode    		 = ConvertNone2Empty(row['DEPARTMENT_CODE'])
						strAlias   		 = ConvertNone2Empty(row['DEPARTMENT_ALIAS'])
						strDivisionAlias = ConvertNone2Empty(row['DIVISION_ALIAS'])

						#Get division ObjectId by division alias in division collection
						ObjectDivisionId 	= self.m_oMongodbController.GetDivisionIdIdByDivsionAlias(oDivisionClt, strDivisionAlias)

						strHrId	 		 = ConvertNone2Empty(str(row['DEPARTMENT_HR_ID']))
						strStatus 		 = ConvertNone2Empty(row['STATUS_DEPARTMENT'])
						strStatus		 = strStatus.lower()
						iStatus			 = UNKNOWN

						if strStatus == "active":
							iStatus  = ACTIVE
						elif strStatus == "inactive":
							iStatus = INACTIVE
						else:
							iStatus = UNKNOWN

						dDeptInfo['code'] 			= strCode
						dDeptInfo['alias'] 		 	= strAlias
						dDeptInfo['division_id'] 	= ObjectDivisionId
						dDeptInfo['division_alias'] = strDivisionAlias
						dDeptInfo['hr_id']			= strHrId
						dDeptInfo['status'] 		= iStatus
						dDeptInfo['deleted'] 		= 0

						#print dDeptInfo
						self.m_oMongodbController.SaveDepartment(oDepartmentClt, dDeptInfo)

						#if strAlias not in arrDeptAlias:
						#	arrDeptAlias.append(strAlias)
					#if len(arrDeptAlias) > 0:
					#	oDepartmentClt.update({'alias':{'$nin':arrDeptAlias}},
					#							   {'$set': {'deleted': 1}},
					#							   	upsert=False,
					#								multi=True)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************************************
	# Function: PushProductInfo
	# Description: push data product info from CMDB
	#*****************************************************************
	def  PushProductInfo(self):
		try:
			oDataResult = None
			#arrProductAlias = []
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oProductCollection	  = PymongoCollection(self.m_oDatabaseMongodb, CLT_PRODUCT, False)
				oDepartmentCollection = PymongoCollection(self.m_oDatabaseMongodb, CLT_DEPARTMENT, False)

				strSQL 		= self.m_oQuery.GetProductQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						dProductInfo 		= dict()
						strCode 	 		= ConvertNone2Empty(row['PRODUCT_CODE'])
						strAlias	 		= ConvertNone2Empty(row['PRODUCT_ALIAS'])
						strDepartmentAlias 	= ConvertNone2Empty(row['OPERATION_DEPARTMENT'])
						strType				= ConvertNone2Empty(row['PRODUCT_TYPE'])
						strStatus			= ConvertNone2Empty(row['STATUS_PRODUCT'])
						strStatus		    = strStatus.lower()

						try:
							iStatus			= PRODUCT_STATUS[strStatus]
						except:
							iStatus			= UNKNOWN

						dProductInfo['code'] 			 = strCode
						dProductInfo['alias']			 = strAlias
						dProductInfo['department_alias'] = strDepartmentAlias
						dProductInfo['status']			 = iStatus
						dProductInfo['type']			 = strType
						dProductInfo['deleted']			 = 0

						#Get Department Info by department alias in department collection
						dDeptInfo			= dict()
						dDeptInfo			= self.m_oMongodbController.GetDeparmentInfoByDepartmentAlias(oDepartmentCollection, strDepartmentAlias)

						if len(dDeptInfo) > 0:
							dProductInfo['department_id']   = dDeptInfo['department_id']
							dProductInfo['department_code'] = ConvertNone2Empty(dDeptInfo['department_code'])
							dProductInfo['division_id']     = dDeptInfo['division_id']
							dProductInfo['division_alias']  = ConvertNone2Empty(dDeptInfo['division_alias'])
						else:
							dProductInfo['department_id']   = None
							dProductInfo['division_id']     = None
							dProductInfo['division_alias']  = ""
							dProductInfo['department_code'] = ""

						self.m_oMongodbController.SaveProduct(oProductCollection, dProductInfo)
						#Track product alias
						#if strAlias not in arrProductAlias:
						#	arrProductAlias.append(strAlias)

					#if len(arrProductAlias) > 0:
					#	oProductCollection.update({'alias':{'$nin':arrProductAlias}},
					#							   {'$set': {'deleted': 1}},
					#							   	upsert=False,
					#								multi=True)

		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************************************
	# Function: PushTechnicalOwnerInfo
	# Description: push data technical owner info from CMDB
	#*****************************************************************
	def PushTechnicalOwnerInfo(self):
		try:
			oDataResult 	 = None
			arrTechnicalName = []
			dTechnicalInfo 	 = dict()
			#Connect to CMDBv2
			if self.IsConnectedToCMDBv2() is False:
				return 0

			#Connect to CMDB database
			if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
				oTechnicalOwnerCollection	= PymongoCollection(self.m_oDatabaseMongodb, CLT_TECHNICAL_OWNER_GROUP, False)
				oProductCollection	  		= PymongoCollection(self.m_oDatabaseMongodb, CLT_PRODUCT, False)

				strSQL 		= self.m_oQuery.GetTechnicalOwnerInfoQuery()
				oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

				if oDataResult is not None:
					for row in oDataResult:
						strName 	 		= ConvertNone2Empty(row['NAME'])
						strDescription 		= ConvertNone2Empty(row['DESCR'])
						strEmailList		= ConvertNone2Empty(row['EMAIL'])
						strProductAlias		= ConvertNone2Empty(row['CORE_PRODUCT'])

						if strName not in dTechnicalInfo.keys():
							dTechnicalInfo[strName] = dict()
							dTechnicalDetail				  = dTechnicalInfo[strName]
							dTechnicalDetail['product_alias'] = []

						dTechnicalDetail = dTechnicalInfo[strName]
						dTechnicalDetail['description']	= strDescription
						dTechnicalDetail['email_list']	= strEmailList

						if strProductAlias not in dTechnicalDetail['product_alias']:
							dTechnicalDetail['product_alias'].append(strProductAlias)

					# Find product_id from product_alias for each technical owner
					for strName in dTechnicalInfo.keys():
						dTechnicalOwnerGroup 				= dict()
						dTechnicalDetail 	 				= dTechnicalInfo[strName]
						dTechnicalOwnerGroup['name'] 		= strName
						dTechnicalOwnerGroup['description'] = dTechnicalDetail['description']
						dTechnicalOwnerGroup['email_list'] 	= dTechnicalDetail['email_list']

						#Get array Product Id from array product alias
						arrProducatAlias 	 				= dTechnicalDetail['product_alias']
						arrProductId	 	 				= self.m_oMongodbController.GetListProductIdIdByListProductAlias(oProductCollection, arrProducatAlias)
						dTechnicalOwnerGroup['product_id']	= arrProductId
						dTechnicalOwnerGroup['deleted']		= 0

						self.m_oMongodbController.SaveTechnicalOwnerGroup(oTechnicalOwnerCollection, dTechnicalOwnerGroup)
						#Track product alias
						if strName not in arrTechnicalName:
							arrTechnicalName.append(strName)

					if len(arrTechnicalName) > 0:
						oTechnicalOwnerCollection.update({'name':{'$nin':arrTechnicalName}},
												   {'$set': {'deleted': 1}},
												   	upsert=False,
													multi=True)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
		finally:
			self.m_oMssqldbModel.Close()

	#*****************************************
	# Function: MigrationData
	# Description: Main Migration Data
	#*****************************************
	def MigrationData(self):
		try:
			self.PushDivisionInfo()
			Utilities.WriteDataLog("Division info was migrated successfully", INFO, self.m_oConfig)
			self.PushDepartmentInfo()
			Utilities.WriteDataLog("Department info was migrated successfully", INFO, self.m_oConfig)
			self.PushProductInfo()
			Utilities.WriteDataLog("Product info was migrated successfully", INFO, self.m_oConfig)
			self.PushTechnicalOwnerInfo()
			Utilities.WriteDataLog("Technical owner group info was migrated successfully", INFO, self.m_oConfig)
			Utilities.WriteDataLog("=========================================", INFO, self.m_oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)

