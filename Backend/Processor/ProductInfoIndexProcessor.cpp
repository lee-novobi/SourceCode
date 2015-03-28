#include "ProductInfoIndexProcessor.h"
#include "ProductIndexPooler.h"
#include "../Controller/ProductInfoChangeController.h"
#include "../Config/ConfigFile.h"
#include "../Model/CIIndexPoolerModel.h"

CProductInfoIndexProcessor::CProductInfoIndexProcessor(const string& strCfgFile)
:CCIInfoIndexProcessor(strCfgFile)
{
	m_pFuncStartPoller = &StartPooler;
	m_pCIInfoChangeController = new CProductInfoChangeController();
}

CProductInfoIndexProcessor::~CProductInfoIndexProcessor(void)
{
}

void* CProductInfoIndexProcessor::StartPooler(void *pData)
{
	CCIIndexPoolerModel* pCIIndexPoolerModel = static_cast<CCIIndexPoolerModel*>(pData);
	cout << "Pooler was born! Be given a vector of: " << pCIIndexPoolerModel->GetLength() << " elements" << endl;	
	if ( pCIIndexPoolerModel->GetLength() > 0 ) {
		 CProductIndexPooler oProductIndexPooler("Config.ini");
		 oProductIndexPooler.ProceedInfo(pCIIndexPoolerModel);		 
	}	
}

void CProductInfoIndexProcessor::ProceedInfoIndex()
{
	m_nInfoIndexPooler = m_pConfigFile->GetProductIndexPooler();
	cout << "Pooler number is:" << m_nInfoIndexPooler << endl;
	
	CCIInfoIndexProcessor::ProceedInfoIndex();
}