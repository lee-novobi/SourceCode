#include "DivisionInfoIndexProcessor.h"
#include "DivisionIndexPooler.h"
#include "../Controller/DivisionInfoChangeController.h"
#include "../Config/ConfigFile.h"
#include "../Model/CIIndexPoolerModel.h"

CDivisionInfoIndexProcessor::CDivisionInfoIndexProcessor(const string& strCfgFile)
:CCIInfoIndexProcessor(strCfgFile)
{
	m_pFuncStartPoller = &StartPooler;
	m_pCIInfoChangeController = new CDivisionInfoChangeController();
}

CDivisionInfoIndexProcessor::~CDivisionInfoIndexProcessor(void)
{
}

void* CDivisionInfoIndexProcessor::StartPooler(void *pData)
{
	CCIIndexPoolerModel* pCIIndexPoolerModel = static_cast<CCIIndexPoolerModel*>(pData);
	cout << "Pooler was born! Be given a vector of: " << pCIIndexPoolerModel->GetLength() << " elements" << endl;
	if ( pCIIndexPoolerModel->GetLength() > 0 ) {
		 CDivisionIndexPooler oDivisionIndexPooler("Config.ini");
		 oDivisionIndexPooler.ProceedInfo(pCIIndexPoolerModel);		 
	}	
}

void CDivisionInfoIndexProcessor::ProceedInfoIndex()
{
	m_nInfoIndexPooler = m_pConfigFile->GetDivisionIndexPooler();	
	
	CCIInfoIndexProcessor::ProceedInfoIndex();
}