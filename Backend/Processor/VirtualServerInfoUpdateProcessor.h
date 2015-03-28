#pragma once
#include "CIInfoUpdateProcessor.h"

class CVirtualServerInfoUpdateProcessor : public CCIInfoUpdateProcessor
{
public:
	CVirtualServerInfoUpdateProcessor(const string& strCfgFile);
	~CVirtualServerInfoUpdateProcessor(void);
};