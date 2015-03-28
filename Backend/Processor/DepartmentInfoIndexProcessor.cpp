#include "DepartmentInfoIndexProcessor.h"
#include "DepartmentIndexPooler.h"
#include "../Controller/DepartmentInfoChangeController.h"
#include "../Config/ConfigFile.h"
#include "../Model/CIIndexPoolerModel.h"

CDepartmentInfoIndexProcessor::CDepartmentInfoIndexProcessor(const string& strCfgFile)
:CCIInfoIndexProcessor(strCfgFile)
{
	m_pFuncStartPoller = &StartPooler;
	m_pCIInfoChangeController = new CDepartmentInfoChangeController();
}

CDepartmentInfoIndexProcessor::~CDepartmentInfoIndexProcessor(void)
{
}

void* CDepartmentInfoIndexProcessor::StartPooler(void *pData)
{
	CCIIndexPoolerModel* pCIIndexPoolerModel = static_cast<CCIIndexPoolerModel*>(pData);
	cout << "Pooler was born! Be given a vector of: " << pCIIndexPoolerModel->GetLength() << " elements" << endl;
	if ( pCIIndexPoolerModel->GetLength() > 0 ) {
		 CDepartmentIndexPooler oDepartmentIndexPooler("Config.ini");
		 oDepartmentIndexPooler.ProceedInfo(pCIIndexPoolerModel);		 
	}
}

void CDepartmentInfoIndexProcessor::ProceedInfoIndex()
{
	m_nInfoIndexPooler = m_pConfigFile->GetDepartmentIndexPooler();	
	CCIInfoIndexProcessor::ProceedInfoIndex();
}