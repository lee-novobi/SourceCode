#include "DepartmentInfoSynchronizationProcessor.h"
#include "../Controller/CollectorController.h"
#include "../Controller/DepartmentController.h"
#include "../Model/CollectorModel.h"
#include "../Synchronization/MISDepartmentSynchronization.h"

CDepartmentInfoSynchronizationProcessor::CDepartmentInfoSynchronizationProcessor(const string& strCfgFile)
:CSynchronizationProcessor(strCfgFile)
{	
	m_pMISDepartmentSync = new CMISDepartmentSynchronization();
}

CDepartmentInfoSynchronizationProcessor::~CDepartmentInfoSynchronizationProcessor(void)
{	
	if (NULL != m_pMISDepartmentSync)
		delete m_pMISDepartmentSync;
}

void CDepartmentInfoSynchronizationProcessor::CompareFullDepartmentData(CCollectorModel* pCollectorInfo)
{
	char *pData;
	char *pTmpDataInfo;
	char *pUrlParameter;
	
	/*BSONObj bsonAPIInfo = BSON(
				"\"active\"" 	<< "true" <<
				"\"orgLevel\"" 			<< "DIV");
	*/
	pUrlParameter = (char*)"?active=true\\&orgLevel=DEPT";
	//pUrlParameter = (char*)bsonAPIInfo.toString().c_str();
	
	cout << pUrlParameter << endl;
	if (m_pMISDepartmentSync->Synchronize(pUrlParameter, pCollectorInfo, &pTmpDataInfo))
	{
		pData = pTmpDataInfo;
		if (NULL != pData)
		{
			//cout << "Data:" << pData << endl;
			m_pDepartmentController->CompareFullData(pCollectorInfo, pData);
			delete pData;
		}
	}
	cout << "Finished" << endl;
}

void CDepartmentInfoSynchronizationProcessor::CompareChangeDepartmentData(CCollectorModel* pCollectorInfo)
{
	char *pData;
	char *pTmpDataInfo;

	time_t tFromDate;
	struct tm* tmFrom;
	string strUrlParameter;
	string strFromDate;
	string strToDate;

	tFromDate = CUtilities::GetDateAgo(400*SEC_PER_DAY);
	tmFrom = CUtilities::GetLocalTime(&tFromDate);
	
	strFromDate = CUtilities::GetSpecialTime("%Y-%m-%d", tmFrom);
	strToDate = CUtilities::GetCurrTime("%Y-%m-%d");

	strUrlParameter = "?from=" + strFromDate + "\\&to=" + strToDate + "\\&orgLevel=DEPT";
	
	if (m_pMISDepartmentSync->Synchronize((char*)strUrlParameter.c_str(), pCollectorInfo, &pTmpDataInfo))
	{
		pData = pTmpDataInfo;
		if (NULL != pData)
		{
			//cout << "Data:" << pData << endl;
			m_pDepartmentController->CompareChangeData(pCollectorInfo, pData);
			delete pData;
		}
	}
	cout << "Finished" << endl;
}