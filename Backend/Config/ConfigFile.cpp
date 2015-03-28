#include "ConfigFile.h"
#include "../Common/DBCommon.h"

#define INVERTED_INDEX_GROUP "INVERTED_INDEX"
#define DIVISION_INDEX_POLLER "DivisionIndexPoller"
#define DEPARTMENT_INDEX_POLLER "DepartmentIndexPoller"
#define SERVER_INDEX_POLLER "ServerIndexPoller"
#define PRODUCT_INDEX_POLLER "ProductIndexPoller"

#define CI_MAP_TMP_GROUP "CI_MAP_TMP"

#define DEBUG_GROUP "DEBUG"
#define DEBUG_LEVEL "Level"

CConfigFile::CConfigFile(const string& strFileName)
:CConfigReader(strFileName)
{	
}

CConfigFile::~CConfigFile(void)
{	
}

string CConfigFile::GetErrorLogFileName()
{   
   return ReadStringValue("ERROR", "ErrorLog");   
}

string CConfigFile::GetHost()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, HOST);
}

string CConfigFile::GetUser()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, USER);
}

string CConfigFile::GetPassword()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, PASS);
}

string CConfigFile::GetSource()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, SRC);
}

string CConfigFile::GetPort()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, PORT);
}

bool CConfigFile::IsReplicateSetUsed()
{
	return ReadBoolValue(MONGODB_CMDB_GROUP, REPLICA_SET);
}

string CConfigFile::GetReadReference()
{
	return ReadStringValue(MONGODB_CMDB_GROUP, READ_REFERENCE);
}

int CConfigFile::GetDivisionIndexPooler()
{
	return ReadIntValue(INVERTED_INDEX_GROUP, DIVISION_INDEX_POLLER);
}

int CConfigFile::GetDepartmentIndexPooler()
{
	return ReadIntValue(INVERTED_INDEX_GROUP, DEPARTMENT_INDEX_POLLER);
}

int CConfigFile::GetServerIndexPooler()
{
	return ReadIntValue(INVERTED_INDEX_GROUP, SERVER_INDEX_POLLER);
}

int CConfigFile::GetProductIndexPooler()
{
	return ReadIntValue(INVERTED_INDEX_GROUP, PRODUCT_INDEX_POLLER);
}

int CConfigFile::GetDebugLevel()
{
	return ReadIntValue(DEBUG_GROUP, DEBUG_LEVEL);
}

string CConfigFile::GetTmpTableName(const char* strCIType)
{
	return ReadStringValue(CI_MAP_TMP_GROUP, strCIType);
}