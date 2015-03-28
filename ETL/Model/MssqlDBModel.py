import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

from inspect import stack
import pymssql
from Config import CConfig
from Utility import Utilities

#**************************************************************
#Class CMssqlDBModel                                          *
#Description: Define base functions of Mssql                  *
#**************************************************************
class CMssqlDBModel:
    def __init__(self):
        self.m_oConfig = CConfig()

    def Connect(self, oDatabase):
        try:
            strDBHost        = oDatabase.Host
            strDBUser        = oDatabase.User
            strDBPassword    = oDatabase.Pass
            strDBSource      = oDatabase.Database
            self.m_connDb    = pymssql.connect(host=strDBHost, user=strDBUser, password=strDBPassword, database=strDBSource, as_dict=True, charset='UTF-8') #'ISO-8859-1'
            self.m_oCursorDb = self.m_connDb.cursor()
            return True
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
            return False

    def Close(self):
        self.m_oCursorDb.close()

    def QueryAllData(self, strSQL):
        try:
            self.m_oCursorDb.execute(strSQL)
            return self.m_oCursorDb.fetchall()
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
            return None

    def QueryOneData(self, strSQL):
        try:
            self.m_oCursorDb.execute(strSQL)
            return self.m_oCursorDb.fetchone()
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
            return None

    def ExecuteSQL(self, strSQL):
        try:
            self.m_oCursorDb.execute(strSQL)
            self.m_connDb.commit()
        except Exception, exc:
            strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
            Utilities.WriteErrorLog(strErrorMsg, self.m_oConfig)
