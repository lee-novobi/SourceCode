#pragma once
#include "CIInfoChangeModel.h"

class CServerInfoChangeModel:
	public CCIInfoChangeModel
{
public:
	CServerInfoChangeModel(void);
	~CServerInfoChangeModel(void);
protected:
	void InitLookUpFieldValue();
	string MapLookUpField(string strFieldName, string strVal);
};