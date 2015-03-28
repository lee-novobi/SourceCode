#include "CMDBController.h"

CCMDBController::CCMDBController(void)
{
}

CCMDBController::~CCMDBController(void)
{
}

auto_ptr<DBClientCursor> CCMDBController::LoadData()
{
	return Find();
}