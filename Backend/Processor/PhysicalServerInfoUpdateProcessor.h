#pragma once
#include "CIInfoUpdateProcessor.h"

class CPhysicalServerInfoUpdateProcessor : public CCIInfoUpdateProcessor
{
public:
	CPhysicalServerInfoUpdateProcessor(const string&);
	~CPhysicalServerInfoUpdateProcessor(void);
};