#include "Processor.h"
#include "../Config/ConfigFile.h"
#include "../Controller/MongodbController.h"

CProcessor::CProcessor(const string& strFileName)
{
	m_pConfigFile = new CConfigFile(strFileName);
}

CProcessor::~CProcessor(void)
{
	if (NULL != m_pConfigFile)
	{
		delete m_pConfigFile;
	}

	m_arrMongodbController.clear();
}

void CProcessor::RegisterController(CMongodbController* pController)
{
	if (NULL != pController)
	{
		m_arrMongodbController.push_back(pController);
	}
}

bool CProcessor::Connect()
{
	bool bResult = true;
	//====================================CMDB Connection==================================
	ConnectInfo CInfo = CDBUtilities::GetConnectInfo(m_pConfigFile);
	
	//================Connect DB=================
	MongodbControllerArray::iterator it;
	CMongodbController* pController = NULL;
	for(int i=0; i<DB_RECONNECT_TIME; i++)
	{
		it = m_arrMongodbController.begin();

		// Connect all controllers to database
		while (it != m_arrMongodbController.end())
		{
			pController = *it;
			if (!pController->Connect(CInfo))
			{
				CUtilities::WriteErrorLog(ERROR_MSG, CUtilities::FormatLog(ERROR_MSG, "CProcessor", "Connect()", "Connection Fail"));
				sleep(DB_CONNECT_RETRY_INTERVAL);

				// Mark as false if one controller could not connect to database
				bResult = false;
				break;
			}
			else
			{
				bResult = true;
				cout << "Connected" << endl;
			}

			it++;
		}
		
		// All controllers were connnected to database
		if (bResult)
		{
			break;
		}		
	}	

	return bResult;
}