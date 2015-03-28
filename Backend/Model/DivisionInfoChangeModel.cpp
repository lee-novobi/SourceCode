#include "DivisionInfoChangeModel.h"

CDivisionInfoChangeModel::CDivisionInfoChangeModel(void)
{
	InitLookUpFieldValue();
}

CDivisionInfoChangeModel::~CDivisionInfoChangeModel(void)
{
}

void CDivisionInfoChangeModel::InitLookUpFieldValue()
{
	m_bIsValueMapping = true;
	m_mapLookUpFieldValue["status"]["0"] = "Inactive";
	m_mapLookUpFieldValue["status"]["1"] = "Active";	
}