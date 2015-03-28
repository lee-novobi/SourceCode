#Description: Load configuration file for all module
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

import ConfigParser
from inspect import stack
from Utility import Utilities
from Constants import *

#Class Config
#Load all parameter in configuration file and return value for each parameter
class CConfig:
	def __init__(self):
		self.LoadConfig()

	def	 LoadConfig(self):
		try:
			oConfig = ConfigParser.ConfigParser()
			fnConfig = os.path.join(os.path.dirname(__file__), 'Config.ini')
			oConfig.read(fnConfig)
			self.LoadLogConfig(oConfig)
			self.LoadCMDBConfig(oConfig)
			self.LoadCMDBv2Config(oConfig)
			self.LoadMDRConfig(oConfig)
			self.LoadZabbixTrapper(oConfig)
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def	 LoadCMDBConfig(self, oConfig):
		try:
			self.m_strCMDBHost			= oConfig.get(CMDB_CONFIG, 'Host', 'localhost')
			self.m_strCMDBUser			= oConfig.get(CMDB_CONFIG, 'User', 'sa')
			self.m_iCMDBPort			= int(oConfig.get(CMDB_CONFIG, 'Port', '1433'))
			self.m_strCMDBPassword		= oConfig.get(CMDB_CONFIG, 'Password', '')
			self.m_strCMDBSource		= oConfig.get(CMDB_CONFIG, 'Source', '')
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def	 LoadCMDBv2Config(self, oConfig):
		try:
			self.m_strCMDBv2Host		= oConfig.get(CMDBv2_CONFIG, 'Host', 'localhost')
			self.m_strCMDBv2User		= oConfig.get(CMDBv2_CONFIG, 'User', 'admin')
			self.m_iCMDBv2Port			= int(oConfig.get(CMDBv2_CONFIG, 'Port', '27017'))
			self.m_strCMDBv2Password	= oConfig.get(CMDBv2_CONFIG, 'Password', '')
			self.m_strCMDBv2Source		= oConfig.get(CMDBv2_CONFIG, 'Source', '')
			self.m_strCMDBv2Uri			= "mongodb://" + self.m_strCMDBv2User + ':' + self.m_strCMDBv2Password
			self.m_strCMDBv2Uri	   		+=	"@" + self.m_strCMDBv2Host + '/' + self.m_strCMDBv2Source
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def	 LoadMDRConfig(self, oConfig):
		try:
			self.m_strMDRHost			= oConfig.get(MDR_CONFIG, 'Host', 'localhost')
			self.m_strMDRUser			= oConfig.get(MDR_CONFIG, 'User', 'sa')
			self.m_iMDRPort				= int(oConfig.get(MDR_CONFIG, 'Port', '3306'))
			self.m_strMDRPassword		= oConfig.get(MDR_CONFIG, 'Password', '')
			self.m_strMDRSource			= oConfig.get(MDR_CONFIG, 'Source', '')
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def LoadLogConfig(self, oConfig):
		try:
			self.m_strErrorLog	= oConfig.get('LOG', 'Error', '')
			self.m_strDataLog	= oConfig.get('LOG', 'DataInfo', '')
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	def  LoadZabbixTrapper(self, oConfig):
		try:
			self.m_strHostTrapper  		= oConfig.get('TRAPPER', 'HostTrapper', 'localhost')
			self.m_strLocationTrapper	= oConfig.get('TRAPPER', 'LocationTrapper', '')
			self.m_strZabbixServer		= oConfig.get('TRAPPER', 'ZabbixServer', '127.0.0.1')
		except Exception, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self)

	@property
	def CMDBHost(self):
		return self.m_strCMDBHost

	@property
	def CMDBUser(self):
		return self.m_strCMDBUser

	@property
	def CMDBPassword(self):
		return self.m_strCMDBPassword

	@property
	def CMDBPort(self):
		return self.m_iCMDBPort

	@property
	def CMDBSource(self):
		return self.m_strCMDBSource

	@property
	def CMDBv2Host(self):
		return self.m_strCMDBv2Host

	@property
	def CMDBv2Uri(self):
		return self.m_strCMDBv2Uri

	@property
	def CMDBv2Port(self):
		return self.m_iCMDBv2Port

	@property
	def CMDBv2Source(self):
		return self.m_strCMDBv2Source

	@property
	def MDRHost(self):
		return self.m_strMDRHost

	@property
	def MDRUser(self):
		return self.m_strMDRUser

	@property
	def MDRPort(self):
		return self.m_iMDRPort

	@property
	def MDRPassword(self):
		return self.m_strMDRPassword

	@property
	def MDRSource(self):
		return self.m_strMDRSource

	@property
	def ErrorLog(self):
		return self.m_strErrorLog

	@property
	def DataLog(self):
		return self.m_strDataLog

	def GetHostTrapper(self):
		return self.m_strHostTrapper

	def GetLocationTrapper(self):
		return self.m_strLocationTrapper

	def GetZabbixServer(self):
		return self.m_strZabbixServer



