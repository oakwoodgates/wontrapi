# WontrapiGo

 Class for accessing the Ontraport API via Ontraport's SDK for PHP. 
 This was created to make the SDK more accessable and add methods for common use cases.

## Changelog

## v.0.5.0
* Introduced:
	* ``` get_forms() ```
	* ``` get_objects_tagged() ```
	* ``` subscribe() ```
	* ``` unsubscribe() ```
	* ``` get_form_blocks() ```
* Helpers:
	* ``` get_count() ```
	* ``` get_object_meta() ```
	* ``` prepare_dropdown_field() ```
	* ``` contact_tag_array() ```

## v0.4.0 
* Introduced:
	* ``` get_contact_id_by_email() ```
	* ``` get_object_id_by_email() ```
	* ``` get_field() ```
	* ``` get_section() ```
	* ``` create_section() ```
* Helpers:
	* ``` get_ids_from_response() ```
	* ``` get_data_from_response() ```
	* ``` prepare_field() ```
	* ``` field_options() ```
	* ``` prepare_section() ```
	* ``` add_col() ```
* Update:
	* Ontraport SDK 1.1.1
* Improve:
	* Rewrite and bug fix many functions to get ready for production use.


## v0.3.3 
* Introduced:
	* ``` transaction_process() ```

* Update:
	* Ontraport SDK 1.0.4


## v0.3.1 
* Helper:
	* ``` contact_has_tag() ```

## v0.3.0 
* Introduced:
	* ``` get_objects() ```
	* ``` get_contacts() ```
	* ``` get_contacts_where() ```
	* ``` get_contacts_by_email() ```
	* ``` get_transactions() ```
	* ``` get_transactions_by_contact_id() ```
* Helpers:
	* ``` get_id_from_response() ```
	* ``` prepare_search_condition() ```
	* ``` prepare_search_condition() ```
	* ``` prepare_search_condition() ```
	* ``` prepare_search_condition() ```
* Update:
	* Ontraport SDK
* Add:
	* WontrapiHelp and helper functions for getting and returning data.
* Improve:
	* How data is returned.

## v0.2.0 

* Introduced:
	* ``` get_landingpage() ```
	* ``` get_landingpage_object_meta() ```
	* ``` get_landingpage_collection_info() ```
	* ``` get_landingpage_hosted_url() ```
	* ``` get_transaction() ```
	* ``` get_order() ```
	* ``` get_transaction_object_meta() ```
	* ``` get_transaction_collection_info() ```
	* ``` transaction_to_collections() ```
	* ``` transaction_to_declined() ```
	* ``` transaction_to_paid() ```
	* ~``` count_landingpages() ```~
	* ~``` get_landingpage_object_meta_fields() ```~
	* ~``` get_transaction_object_meta_fields() ```~

## v0.1.0 

* Introduced:
	* ``` create_object() ```
	* ``` create_or_update_object() ```
	* ``` get_object() ```
	* ``` get_object_meta() ```
	* ``` get_object_collection_info() ```
	* ``` update_object() ```
	* ``` delete_object() ```
	* ``` create_contact() ```
	* ``` create_or_update_contact() ```
	* ``` get_contact() ```
	* ``` update_contact() ```
	* ``` delete_contact() ```
	* ``` get_contact_object_meta() ```
	* ``` get_contact_collection_info() ```
	* ``` add_to_sequence() ```
	* ``` remove_from_sequence() ```
	* ``` tag() ```
	* ``` untag() ```
	* ``` get_form() ```
	* ``` get_form_collection_info() ```
	* ``` get_smartform_object_meta() ```
	* ``` get_smartform_html() ```
	* ~``` get_object_meta_data_object() ```~
	* ~``` get_object_meta_fields() ```~
	* ~``` count_objects() ```~
	* ~``` get_smartform_object_meta_fields() ```~
	* ~``` count_forms() ```~
	* ~``` count_contacts() ```~
	* ~``` get_contact_object_meta_fields() ```~
	* ~``` add_tag_to_contact() ```~
	* ~``` remove_tag_from_contact() ```~
* Helpers:
	* ``` objectID() ```
* Methods for setting app id, etc
* Connect to Ontraport
* Include [Ontraport's PHP SDK](https://github.com/Ontraport/SDK-PHP) 
