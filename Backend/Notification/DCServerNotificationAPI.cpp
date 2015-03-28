#include "DCServerNotificationAPI.h"
#include "../Model/NotificationModel.h"
#include "../Common/DBCommon.h"

CDCServerNotificationAPI::CDCServerNotificationAPI(void)
{
}

CDCServerNotificationAPI::~CDCServerNotificationAPI(void)
{
}

string CDCServerNotificationAPI::Convert2JSON(CNotificationModel* pData)
{
	BSONObj boData, boResult, boTemp;
	/*vector<BSONElement> bePriInterface, bePubInterface;
	BSONArrayBuilder babInterface;*/
	
	boData = *pData;
	/*if(boData["private_interface"].type() == BSON_ARRAY_TYPE){
		bePriInterface = boData["private_interface"].Array();
	}
	if(boData["public_interface"].type() == BSON_ARRAY_TYPE){
		bePubInterface = boData["public_interface"].Array();
	}
	bePriInterface.insert(bePriInterface.end(), bePubInterface.begin(), bePubInterface.end());
	for(int i = 0; i < bePriInterface.size(); i++)
	{
		babInterface << bePriInterface[i];
	}*/

	boResult = BSON(
		"SerialNumber"		<< boData["serial"] <<
		"ServerName"		<< boData["server_name"] <<
		"Model"				<< boData["server_model"] <<
		"Site"				<< boData["site"] <<
		"Rack"				<< boData["rack"] <<
		"RackUnit"			<< boData["u"] <<
		"IPChassis"			<< boData["ip_chassis"] <<
		"Bay"				<< boData["bay"] <<
		"TechnicalOwner"	<< boData["technical_owner"] <<
		"IPConsole"			<< boData["ip_console"] <<
		"MacAddress"		<< boData["mac_address"]
		);
	
	return boResult.jsonString();
}
