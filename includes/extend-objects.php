<?php
class Wontrapi_Contacts extends Wontrapi_Objects {
	protected static $object_id = 0;

	public function add_tag ( $id, $tag_id ) {
		return add_tag_to_object ( $id, $tag_id );
	}

	public function remove_tag ( $id, $tag_id ) {
		return remove_tag_to_object ( $id, $tag_id );
	}
}

class Wontrapi_Notes extends Wontrapi_Objects {
	protected static $object_id = 12;
}

class Wontrapi_Products extends Wontrapi_Objects {
	protected static $object_id = 16;
}

class Wontrapi_Purchases extends Wontrapi_Objects {
	protected static $object_id = 17;
}

class Wontrapi_Shippings extends Wontrapi_Objects {
	protected static $object_id = 64;
}

class Wontrapi_Staff extends Wontrapi_Objects {
	protected static $object_id = 2;
}

class Wontrapi_Tags extends Wontrapi_Objects {
	protected static $object_id = 14;
}

class Wontrapi_Taxes extends Wontrapi_Objects {
	protected static $object_id = 63;
}
