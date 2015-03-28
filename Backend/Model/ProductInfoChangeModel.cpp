#include "ProductInfoChangeModel.h"

CProductInfoChangeModel::CProductInfoChangeModel(void)
{
	InitLookUpFieldValue();
}

CProductInfoChangeModel::~CProductInfoChangeModel(void)
{
}

void CProductInfoChangeModel::InitLookUpFieldValue()
{
	m_bIsValueMapping = true;
	m_mapLookUpFieldValue["status"]["0"] = "New";
	m_mapLookUpFieldValue["status"]["1"] = "In Used";
	m_mapLookUpFieldValue["status"]["2"] = "Transferring";
	m_mapLookUpFieldValue["status"]["3"] = "Remove";
	m_mapLookUpFieldValue["status"]["4"] = "Close";	
}