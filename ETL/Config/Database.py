#Description: Database Object
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

from inspect import stack
from Utility import Utilities

#**************************************************************
#Class CDatabase                                              *
#Description: Define Connection string to connect database    *
#**************************************************************
class CDatabase:
    def __init__(self, strHost, strUser, strPass, iPort, strDatabase):
        self.m_strHost     = strHost
        self.m_strUser     = strUser
        self.m_strPass     = strPass
        self.m_iPort       = int(iPort)
        self.m_strDatabase = strDatabase

    @property
    def Host(self):
        return self.m_strHost
    @Host.setter
    def Host(self, strHost):
        self.m_strHost = strHost

    @property
    def User(self):
        return self.m_strUser
    @User.setter
    def User(self, strUser):
        self.m_strUser = strUser

    @property
    def Pass(self):
        return self.m_strPass
    @Pass.setter
    def Pass(self, strPass):
        self.m_strPass = strPass

    @property
    def Port(self):
        return self.m_iPort
    @Port.setter
    def Port(self, iPort):
        self.m_iPort = iPort

    @property
    def Database(self):
        return self.m_strDatabase
    @Database.setter
    def Database(self, strDatabase):
        self.m_strDatabase = strDatabase