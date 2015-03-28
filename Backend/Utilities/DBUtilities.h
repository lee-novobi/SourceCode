#include <iostream>
#include <fstream>
#include <stdio.h>
#include <stdlib.h>
#include <iconv.h>
#include <boost/algorithm/string.hpp>
#include <boost/algorithm/string/split.hpp>
#include <iterator> // for ostream_iterator
#include <vector>
#include <map>
#include <boost/regex.hpp>
#include <string>
#include <sys/types.h>
#include <sys/wait.h>
#include <dirent.h>

using namespace std;

class CConfigFile;

//===Struct===

struct ConnectInfo
{
	string strHost;
	string strUser;
	string strPass;
	string strSource;
	string strPort;
	string strDBName;
	bool bIsReplicateSetUsed;
	string strReadReferenceOption;
};

class CDBUtilities
{

	public: 
		CDBUtilities();
		virtual ~CDBUtilities();
		
		static ConnectInfo GetConnectInfo(CConfigFile *pConfigFile);
};
