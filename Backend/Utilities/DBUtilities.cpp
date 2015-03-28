#include "DBUtilities.h"
#include "../Common/DBCommon.h"
#include "../Config/ConfigFile.h"


/*
 * Constructor - Assign a file name
 */
CDBUtilities::CDBUtilities()
{

}

/*
 * Destructor -
 */
CDBUtilities::~CDBUtilities()
{

}

ConnectInfo CDBUtilities::GetConnectInfo(CConfigFile *pConfigFile)
{
	ConnectInfo CInfo;
	CInfo.strHost = pConfigFile->GetHost();
	CInfo.strUser = pConfigFile->GetUser();
	CInfo.strPass = pConfigFile->GetPassword();
	CInfo.strSource = pConfigFile->GetSource();
	CInfo.bIsReplicateSetUsed = pConfigFile->IsReplicateSetUsed();	
	CInfo.strReadReferenceOption = pConfigFile->GetReadReference();
	
	if(pConfigFile->GetPort().compare("") != 0)
	{
		CInfo.strHost = CInfo.strHost + ":" + pConfigFile->GetPort();
	}

	return CInfo;
}
