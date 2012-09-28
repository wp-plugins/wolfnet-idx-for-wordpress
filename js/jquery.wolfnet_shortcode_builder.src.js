/**
 * This jQuery script defines the functionality of the WolfNet Shortcode Builder tinyMCE button.
 *
 * @title         jquery.wolfnet_shortcode_builder.src.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *                
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *                
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *                
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

 /* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if ( typeof String.prototype.trim !== 'function' ) {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
( function ( $ ) {

	$.widget( "ui.wolfnetShortcodeBuilder", $.ui.dialog, {

		options : {
			autoOpen     : false,
			height       : 450,
			width        : 475,
			modal        : true,
			defaultTitle : 'WolfNet Shortcode Builder',
			elmPrefix    : 'wolfnetShortcodeBuilder_',
			rootUri      : '',
			loaderUri    : '',
			loaderId     : 'loaderImage',
			menuId       : 'menuPage',
			pageSuffix   : '_page',
			menuItems    : {
				featuredListings : {
					buttonLabel : 'Add Featured Listings',
					shortcode   : 'wnt_featured',
					pageTitle   : 'Featured Listing Shortcode',
					uri         : '-options-featured'
				},
				listingGrid : {
					buttonLabel : 'Add Listing Grid',
					shortcode   : 'wnt_grid',
					pageTitle   : 'Listing Grid Shortcode',
					uri         : '-options-grid'
				},
				propertyList : {
					buttonLabel : 'Add Property List',
					shortcode   : 'wnt_list',
					pageTitle   : 'Property List Shortcode',
					uri         : '-options-list'
				},
				quickSearch : {
					buttonLabel : 'Add QuickSearch',
					shortcode   : 'wnt_search',
					pageTitle   : 'QuickSearch Shortcode',
					uri         : '-options-quicksearch'
				}
			}
		},

		shortcode : '',

		_create : function ()
		{
			var  widget    = this;
			var  option    = this.options;
			var  container = this.element;

			widget._createLoaderImage();
			widget._createMenuPage();
			widget._establishEvents();
			widget._activePage = option.menuId;

			option.title = option.defaultTitle;

			$.ui.dialog.prototype._create.call( this );
		},

		_establishEvents : function ()
		{
			var widget     = this;
			var $container = $( this.element );

			$container.bind( 'insertShortcodeEvent', function () {
				widget.insertShortcode();
				widget.close();
			} );

		},

		_createMenuPage : function ()
		{
			var widget     = this;
			var option     = this.options;
			var container  = this.element;
			var $container = $( container );
			var menuPageId = option.elmPrefix + option.menuId + option.pageSuffix;
			var $menuPage  = container.find( '#' + menuPageId );
			var menuItems  = option.menuItems;
			var $button    = null;

			if ( $menuPage.length == 0 ) {

				$menuPage = $( '<div/>' );
				$menuPage.attr( 'id', menuPageId );

				for ( var pageId in menuItems ) {

					$button = $( '<button/>' );
					$button.html( menuItems[pageId].buttonLabel );
					$button.addClass( 'pageButton' );
					$button.appendTo( $menuPage );
					$button[0].pageId = pageId;
					$button.click( function () {
						widget.openPage( this.pageId );
					} );

				}

				$menuPage.appendTo( $container );

			}

		},

		_createLoaderImage : function ()
		{
			var widget    = this;
			var option    = this.options;
			var container = this.element;
			var loaderId  = option.elmPrefix + option.loaderId ;
			var $loader   = $( '#' + loaderId );

			/* If the window element doesn't exist create it and add it to the page. */
			if ( $loader.length == 0 ) {
				$loader = $( '<div/>' );
				$loader.append( $( '<img src="' + option.loaderUri + '" />' ) );
				$loader.attr( 'id', loaderId );
				$loader.addClass( 'wolfnet_loaderImage' );
				$loader.hide();
				$loader.appendTo( container );
			}

			/* Store a reference to the loader image in memory. */
			widget.loaderImage = $loader;
		},

		_createPage : function ( page )
		{
			var widget        = this;
			var option        = this.options;
			var container     = this.element;
			var $container    = $( container );
			var $loaderImg    = widget.loaderImage;
			var $pageTitle    = null;
			var $backButton   = null;
			var $insertButton = null;

			if ( ( 'uri' in option.menuItems[page] ) && option.menuItems[page].uri != '') {

				var pageUri = option.rootUri + option.menuItems[page].uri;

				$page = $( '<div/>' );
				$page.attr( 'id', option.elmPrefix + page + option.pageSuffix );
				$page.attr( 'class', ( option.elmPrefix + option.pageSuffix ).replace( '__', '_' ) );
				$page.appendTo( $container );

				$backButton = $( '<button/>' );
				$backButton.html( 'Back' );
				$backButton.appendTo( $page );
				$backButton.click( function () {
					widget.closePage();
				} );

				$.ajax( {
					type: 'GET',
					dataType: 'html',
					url: pageUri,
					cache: false,
					beforeSend: function () {
						$page.hide();
						$loaderImg.show();
					},
					success: function ( data ) {
						$page.append( data );
						wolfnet.initMoreInfo( $page.find( '.wolfnet_moreInfo' ) );
						$loaderImg.hide();
						$page.show();

						$insertButton = $( '<button/>' );
						$insertButton.html( 'Insert Shortcode' );
						$insertButton.appendTo( $page );
						$insertButton.click( function () {
							widget._buildShortcode( page );
						} );

					}
				} );

			}
		},

		_getPage : function ( page )
		{
			var option = this.options;
			var pageId = option.elmPrefix + page + option.pageSuffix;
			return $( '#' + pageId );
		},

		openPage : function ( page )
		{
			var widget      = this;
			var option      = this.options;
			var container   = this.element;
			var $container = $( container );
			var $activePage = widget._getPage( widget._activePage );

			if ( page != widget._activePage ) {

				var $page = widget._getPage( page );
				widget._activePage = page;
				$activePage.hide();

				if ( page in option.menuItems && 'pageTitle' in option.menuItems[page] ) {
					widget._setOption( 'title', option.defaultTitle + ': ' + option.menuItems[page].pageTitle );
				}
				else {
					widget._setOption( 'title', option.defaultTitle );
				}

				if ( $page.length == 0 ) {
					widget._createPage( page );
					$page = widget._getPage( page );
				}
				else {
					$page.show();
				}

			}

		},

		closePage : function ()
		{
			var widget      = this;
			var option      = this.options;
			widget.openPage( option.menuId );
		},

		_buildShortcode : function ( page )
		{
			var widget   = this;
			var option   = widget.options;
			var $page    = widget._getPage( page );
			var attrs    = {};
			var code     = '[' + option.menuItems[page].shortcode + ' /]';
			var exclAttr = ['mode','savedsearch','criteria'];
			var $advMode = $page.find( 'input[type="radio"][name="mode"][value="advanced"]:first:checked' );
			var $savSrch = $page.find( 'select[name="savedsearch"]:first' );
			var $loaderImg    = widget.loaderImage;

			$loaderImg.show();

			$page.find( 'input, select' ).each( function () {

				if ( this.name != '' && $.inArray( this.name, exclAttr ) == -1 ) {

					switch ( this.type ) {

						default:
							if ( this.value.trim() != '' ) {
								attrs[this.name] = this.value.trim();
							}
							break;

						case 'checkbox':
						case 'radio':
							if ( this.checked == true ) {
								attrs[this.name] = this.value;
							}
							break;

					}

				}

			} );

			if ( $advMode.length != 0 && $savSrch.length != 0 ) {

				delete attrs.zipcode;
				delete attrs.city;
				delete attrs.minprice;
				delete attrs.maxprice;

				$.ajax( {
					url: option.rootUri + '-saved-search',
					type: 'GET',
					dataType: 'json',
					data: { ID: $savSrch.val() },
					success: function ( data ) {
						for ( var field in data ) {
							attrs[field] = data[field];
						}
						widget._buildShortcodeString( attrs, code );
					},
					complete: function () {
						$loaderImg.hide();
					}
				} );

			}
			else {

				widget._buildShortcodeString( attrs, code );
				$loaderImg.hide();

			}

		},

		_buildShortcodeString : function ( attrs, code )
		{
			var widget     = this;
			var $container = $( widget.element );
			var string     = code;

			for ( var attr in attrs ) {
				string = string.replace( '/]', ' ' + attr + '="' + attrs[attr] + '" /]' );
			}

			widget.shortcode = string;
			$container.trigger( 'insertShortcodeEvent' );

		},

		insertShortcode : function ()
		{
			this.options.tinymce.execCommand( 'mceInsertContent', false, this.shortcode );
		}

	} );

} )( jQuery );
