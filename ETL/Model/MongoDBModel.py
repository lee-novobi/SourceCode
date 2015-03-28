from datetime import datetime
from mongokit import Document
import bson

#Class CDivision mapping division collection
class CDivision(Document):
	def  __init__(self, oZCollection):
		super(CDivision, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'code': basestring,
		'name': basestring,
		'alias': basestring,
		'hr_id': basestring,
		'status': int,
		'deleted': int
	}
	#required_fields = ['alias', 'hr_id']
	default_values = {
		'code': "",
		'name': "",
		'alias': "",
		'status': 1,
		'deleted': 0
	}

#Class CDepartment mapping department collection
class CDepartment(Document):
	def  __init__(self, oZCollection):
		super(CDepartment, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'code': basestring,
		'name': basestring,
		'alias': basestring,
		'fa_code': basestring,
		'fa_alias': basestring,
		'division_id': bson.objectid.ObjectId,
		'division_alias': basestring,
		'status': int,
		'hr_id': basestring,
		'deleted': int
	}
	#required_fields = ['alias', 'hr_id', 'division_alias']
	default_values = {
		'code': "",
		'name': "",
		'alias': "",
		'fa_code': "",
		'fa_alias': "",
		'division_id': None,
		'division_alias': "",
		'status': 1,
		'deleted': 0
	}

#Class CProduct mapping product collection
class CProduct(Document):
	def  __init__(self, oZCollection):
		super(CProduct, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'code': basestring,
		'alias': basestring,
		'fa_code': basestring,
		'fa_alias': basestring,
		'department_id': bson.objectid.ObjectId,
		'department_alias': basestring,
		'department_code': basestring,
		'division_id': bson.objectid.ObjectId,
		'division_alias': basestring,
		'status': int,
		'type': basestring,
		'deleted': int
	}
	#required_fields = ['alias']
	default_values = {
		'code': "",
		'alias': "",
		'fa_code': "",
		'fa_alias': "",
		'department_id': None,
		'department_alias': "",
		'department_code': "",
		'division_id': None,
		'division_alias': "",
		'status': 0,
		'type': "",
		'deleted': 0
	}

#Class CServer mapping server collection
class CServer(Document):
	def  __init__(self, oZCollection):
		super(CServer, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'code': basestring,
		'asset_code': basestring,
		'server_name': basestring,
		'os_server_name': basestring,
		'site': basestring,
		'rack_id': bson.objectid.ObjectId,
		'chassis_id': bson.objectid.ObjectId,
		'rack': basestring,
		'u': int,
		'bay': int,
		'chassis': basestring,
		'bucket': basestring,
		'server_type': int,
		'purpose_use': basestring,
		'product_id': bson.objectid.ObjectId,
		'product_alias': basestring,
		'product_code': basestring,
		'department_alias': basestring,
		'department_code': basestring,
		'division_alias': basestring,
		'server_model': basestring,
		'cpu_config': basestring,
		'memory_size': basestring,
		'ram_config': basestring,
		'hdd_size': basestring,
		'hdd_raid': basestring,
		'ip_console': basestring,
		'os': basestring,
		'software_list': basestring,
		'status': int,
		'note': basestring,
		'created_date': int,
		'last_updated': int,
		'vm_center': basestring,
		'vm_key': basestring,
		'vm_id': basestring,
		'vid': basestring,
		'physical_ip': basestring,
		'physical_SN': basestring,
		'vmtool': basestring,
		'technical_group_id': bson.objectid.ObjectId,
		'technical_group_name': basestring,
		'p_vlan_public': basestring,
		'p_vlan_private': basestring,
		'power_status': int,
		'public_interface': list,
		'private_interface': list,
		'deleted': int
	}
	#required_fields = ['code', 'server_type']
	default_values = {
		'code': "",
		'asset_code': "",
		'server_name': "",
		'os_server_name': "",
		'site': "",
		'rack_id': None,
		'chassis_id': None,
		'rack': "",
		'u': 0,
		'bay': 0,
		'chassis': "",
		'bucket': "",
		'purpose_use': "",
		'product_id': None,
		'product_alias': "",
		'product_code': "",
		'department_alias': "",
		'department_code': "",
		'division_alias': "",
		'server_model': "",
		'cpu_config': "",
		'memory_size': "",
		'ram_config': "",
		'hdd_size': "",
		'hdd_raid': "",
		'ip_console': "",
		'os': "",
		'software_list': "",
		'status': 0,
		'note': "",
		'created_date': 0,
		'last_updated': 0,
		'vm_center': "",
		'vm_key': "",
		'vm_id': "",
		'vid': "",
		'physical_ip': "",
		'physical_SN': "",
		'vmtool': "",
		'technical_group_id': None,
		'technical_group_name': "",
		'p_vlan_public': "",
		'p_vlan_private': "",
		'power_status': 1,
		'public_interface': [],
		'private_interface': [],
		'deleted': 0
	}

#Class CTechnicalOwnerGroup mapping technical_owner_group collection
class CTechnicalOwnerGroup(Document):
	def  __init__(self, oZCollection):
		super(CTechnicalOwnerGroup, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'name': basestring,
		'description': basestring,
		'email_list': basestring,
		'product_id': list,
		'technicals': list,
		'deleted': int
	}
	#required_fields = ['alias', 'hr_id', 'division_alias']
	default_values = {
		'name': "",
		'description': "",
		'email_list': "",
		'product_id': [],
		'technicals': [],
		'deleted': 0
	}

#Class CUser mapping user collection
class CUser(Document):
	def  __init__(self, oZCollection):
		super(CUser, self).__init__(
          doc=None, gen_skel=True, collection=oZCollection, lang='en', fallback_lang='en'
        )
	structure = {
		'username': basestring,
		'full_name': basestring,
		'department': basestring,
		'department_hr_code': basestring,
		'roles': list
	}
	#required_fields = ['alias', 'hr_id', 'division_alias']
	default_values = {
		'username': "",
		'full_name': "",
		'department': "",
		'roles': []
	}