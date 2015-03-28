#include "DivisionInfoSynchronizationProcessor.h"
#include "../Controller/CollectorController.h"
#include "../Controller/DivisionController.h"
#include "../Model/CollectorModel.h"
#include "../Synchronization/MISDivisionSynchronization.h"

CDivisionInfoSynchronizationProcessor::CDivisionInfoSynchronizationProcessor(const string& strCfgFile)
:CSynchronizationProcessor(strCfgFile)
{	
	m_pMISDivisionSync = new CMISDivisionSynchronization();
}

CDivisionInfoSynchronizationProcessor::~CDivisionInfoSynchronizationProcessor(void)
{	
	if (NULL != m_pMISDivisionSync)
		delete m_pMISDivisionSync;
}

void CDivisionInfoSynchronizationProcessor::CompareFullDivisionData(CCollectorModel* pCollectorInfo)
{
	char *pData;
	char *pTmpDataInfo;
	char *pUrlParameter;
	
	/*BSONObj bsonAPIInfo = BSON(
				"\"active\"" 	<< "true" <<
				"\"orgLevel\"" 			<< "DIV");
	*/
	pUrlParameter = (char*)"?active=true\\&orgLevel=DIV";
	//pUrlParameter = (char*)bsonAPIInfo.toString().c_str();
	
	if (m_pMISDivisionSync->Synchronize(pUrlParameter, pCollectorInfo, &pTmpDataInfo))
	{
		pData = pTmpDataInfo;
		if (NULL != pData)
		{
			cout << "Data:" << pData << endl;
			m_pDivisionController->CompareFullData(pCollectorInfo, pData);
			delete pData;
		}
	}
	cout << "Finished" << endl;
}

void CDivisionInfoSynchronizationProcessor::CompareChangeDivisionData(CCollectorModel* pCollectorInfo)
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

	strUrlParameter = "?from=" + strFromDate + "\\&to=" + strToDate + "\\&orgLevel=DIV";

	if (m_pMISDivisionSync->Synchronize((char*)strUrlParameter.c_str(), pCollectorInfo, &pTmpDataInfo))
	{
		pData = pTmpDataInfo;
		if (NULL != pData)
		{
			cout << "Data:" << pData << endl;
			m_pDivisionController->CompareChangeData(pCollectorInfo, pData);
			delete pData;
		}
	}
	cout << "Finished" << endl;
}