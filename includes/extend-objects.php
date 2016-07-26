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
