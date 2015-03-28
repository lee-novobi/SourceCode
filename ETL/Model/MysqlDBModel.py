# Description: Include some function connection database
# and implement transaction with mysql server
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Config'))
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

import MySQLdb
import MySQLdb.cursors
from inspect import stack
from Config import CConfig
from Utility import Utilities

#Class MySqlDataBase
#Interaction and transaction with mysql server
class CMysqlDBModel:
	def __init__(self):
		# Create config object
		self._oConfig = CConfig()

	def Connect(self, oDatabase):
		try:
			strDBHost        = oDatabase.Host
			strDBUser        = oDatabase.User
			strDBPassword    = oDatabase.Pass
			strDBSource      = oDatabase.Database

			self._connDb	= MySQLdb.connect(strDBHost, strDBUser, strDBPassword, strDBSource, cursorclass=MySQLdb.cursors.DictCursor)
			self._connDb.set_character_set('utf8')
			self._oCursorDb = self._connDb.cursor()
			self._oCursorDb.execute('SET NAMES utf8;')
			self._oCursorDb.execute('SET CHARACTER SET utf8;')
			self._oCursorDb.execute('SET character_set_connection=utf8;')
			return True
		except MySQLdb.Error, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
			return False

	def Close(self):
		self._oCursorDb.close()

	def QueryAllData(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			return self._oCursorDb.fetchall()
		except MySQLdb.Error, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
			return None

	def QueryOneData(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			return self._oCursorDb.fetchone()
		except MySQLdb.Error, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
			return None

	def	 ExecuteSQL(self, strSQL):
		try:
			self._oCursorDb.execute(strSQL)
			self._connDb.commit()
		except MySQLdb.Error, exc:
			strErrorMsg = '%s.%s Error: %s - Line: %s' % (self.__class__.__name__, str(exc), stack()[0][3], sys.exc_traceback.tb_lineno) # give a error message
			Utilities.WriteErrorLog(strErrorMsg, self._oConfig)
