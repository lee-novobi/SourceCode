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
from MongoDBController import CMongodbController
from TransportData import CTransportData
import json
from netaddr import *

ConvertNone2Empty = lambda value_object:"" if value_object is None else value_object
#**************************************************************
#Class CTransportUserInfo                                     *
#Description: Define transport user info                      *
#**************************************************************
class CTransportUserInfo(CTransportData):
    def __init__(self):
        super(CTransportUserInfo, self).__init__()

    #*****************************************************************
    # Function: PushUserInfo
    # Description: push data user info
    #*****************************************************************
    def  PushUserInfo(self):
        try:
            oDataResult = None
            #Connect to CMDBv2
            if self.IsConnectedToCMDBv2() is False:
                return 0

            #Connect to CMDB database
            if self.m_oMssqldbModel.Connect(self.m_oCMDBDriver):
                oUserCollection            = PymongoCollection(self.m_oDatabaseMongodb, CLT_USER, False)

                if oUserCollection is None:
                    strErrorMsg = "Cannot connect to %s " % self.m_oConfig.CMDBv2Host
                    Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
                    return 0

                strSQL = self.m_oQuery.GetUserInfoQuery()
                oDataResult = self.m_oMssqldbModel.QueryAllData(strSQL)

                if oDataResult is not None:
                    for row in oDataResult:
                        dUserInfo = dict()
                        strUserName   = ConvertNone2Empty(row['USERNAME'])
                        strFullName   = ConvertNone2Empty(row['FULL_NAME'])
                        strDepartment = ConvertNone2Empty(row['DEPARTMENT'])
                        strFullName   = Utilities.ConvertToUTF8(strFullName)

                        dUserInfo["username"]   = strUserName;
                        dUserInfo["full_name"]  = strFullName;
                        dUserInfo["department"] = strDepartment;
                        self.m_oMongodbController.SaveUserInfo(oUserCollection, dUserInfo)

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
            self.PushUserInfo()
            Utilities.WriteDataLog("User info was migrated successfully", INFO, self.m_oConfig)
            Utilities.WriteDataLog("=========================================", INFO, self.m_oConfig)
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)