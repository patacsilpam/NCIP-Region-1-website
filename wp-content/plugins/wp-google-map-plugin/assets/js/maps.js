/**
 * This jQuery plugin displays map and it's components.
 * @author Flipper Code (hello *at* flippercode *dot* com)
 * @version 1.0
 */
(function($, window, document, undefined) {
	"use strict";	

    function wpgmpInitializeInfoBox() {

        var InfoBox = function(opt_opts) {

            opt_opts = opt_opts || {};
        
            google.maps.OverlayView.apply(this, arguments);
        
            // Standard options (in common with google.maps.InfoWindow):
            //
            this.content_ = opt_opts.content || "";
            this.disableAutoPan_ = opt_opts.disableAutoPan || false;
            this.maxWidth_ = opt_opts.maxWidth || 0;
            this.pixelOffset_ = opt_opts.pixelOffset || new google.maps.Size(0, 0);
            this.position_ = opt_opts.position || new google.maps.LatLng(0, 0);
            this.zIndex_ = opt_opts.zIndex || null;
        
            // Additional options (unique to InfoBox):
            //
            this.boxClass_ = opt_opts.boxClass || "infoBox";
            this.boxStyle_ = opt_opts.boxStyle || {};
            this.closeBoxMargin_ = opt_opts.closeBoxMargin || "2px";
            this.closeBoxURL_ = opt_opts.closeBoxURL || "//www.google.com/intl/en_us/mapfiles/close.gif";
            if (opt_opts.closeBoxURL === "") {
                this.closeBoxURL_ = "";
            }
            this.closeBoxTitle_ = opt_opts.closeBoxTitle || " Close ";
            this.infoBoxClearance_ = opt_opts.infoBoxClearance || new google.maps.Size(1, 1);
        
            if (typeof opt_opts.visible === "undefined") {
                if (typeof opt_opts.isHidden === "undefined") {
                    opt_opts.visible = true;
                } else {
                    opt_opts.visible = !opt_opts.isHidden;
                }
            }
            this.isHidden_ = !opt_opts.visible;
        
            this.alignBottom_ = opt_opts.alignBottom || false;
            this.pane_ = opt_opts.pane || "floatPane";
            this.enableEventPropagation_ = opt_opts.enableEventPropagation || false;
        
            this.div_ = null;
            this.closeListener_ = null;
            this.moveListener_ = null;
            this.contextListener_ = null;
            this.eventListeners_ = null;
            this.fixedWidthSet_ = null;
        }

        InfoBox.prototype = new google.maps.OverlayView();
        
        /**
         * Creates the DIV representing the InfoBox.
         * @private
         */
        InfoBox.prototype.createInfoBoxDiv_ = function() {
        
            var i;
            var events;
            var bw;
            var me = this;
        
            // This handler prevents an event in the InfoBox from being passed on to the map.
            //
            var cancelHandler = function(e) {
                e.cancelBubble = true;
                if (e.stopPropagation) {
                    e.stopPropagation();
                }
            };
        
            // This handler ignores the current event in the InfoBox and conditionally prevents
            // the event from being passed on to the map. It is used for the contextmenu event.
            //
            var ignoreHandler = function(e) {
        
                e.returnValue = false;
        
                if (e.preventDefault) {
        
                    e.preventDefault();
                }
        
                if (!me.enableEventPropagation_) {
        
                    cancelHandler(e);
                }
            };
        
            if (!this.div_) {
        
                this.div_ = document.createElement("div");
        
                this.setBoxStyle_();
        
                if (typeof this.content_.nodeType === "undefined") {
                    this.div_.innerHTML = this.getCloseBoxImg_() + this.content_;
                } else {
                    this.div_.innerHTML = this.getCloseBoxImg_();
                    this.div_.appendChild(this.content_);
                }
        
                // Add the InfoBox DIV to the DOM
                this.getPanes()[this.pane_].appendChild(this.div_);
        
                this.addClickHandler_();
        
                if (this.div_.style.width) {
        
                    this.fixedWidthSet_ = true;
        
                } else {
        
                    if (this.maxWidth_ !== 0 && this.div_.offsetWidth > this.maxWidth_) {
        
                        this.div_.style.width = this.maxWidth_;
                        this.div_.style.overflow = "auto";
                        this.fixedWidthSet_ = true;
        
                    } else { // The following code is needed to overcome problems with MSIE
        
                        bw = this.getBoxWidths_();
        
                        this.div_.style.width = (this.div_.offsetWidth - bw.left - bw.right) + "px";
                        this.fixedWidthSet_ = false;
                    }
                }
        
                this.panBox_(this.disableAutoPan_);
        
                if (!this.enableEventPropagation_) {
        
                    this.eventListeners_ = [];
        
                    // Cancel event propagation.
                    //
                    // Note: mousemove not included (to resolve Issue 152)
                    events = ["mousedown", "mouseover", "mouseout", "mouseup",
                        "click", "dblclick", "touchstart", "touchend", "touchmove"
                    ];
        
                    for (i = 0; i < events.length; i++) {
        
                        this.eventListeners_.push(google.maps.event.addDomListener(this.div_, events[i], cancelHandler));
                    }
        
                    // Workaround for Google bug that causes the cursor to change to a pointer
                    // when the mouse moves over a marker underneath InfoBox.
                    this.eventListeners_.push(google.maps.event.addDomListener(this.div_, "mouseover", function(e) {
                        this.style.cursor = "default";
                    }));
                }
        
                this.contextListener_ = google.maps.event.addDomListener(this.div_, "contextmenu", ignoreHandler);
        
                /**
                 * This event is fired when the DIV containing the InfoBox's content is attached to the DOM.
                 * @name InfoBox#domready
                 * @event
                 */
                google.maps.event.trigger(this, "domready");
            }
        };
        
        /**
         * Returns the HTML <IMG> tag for the close box.
         * @private
         */
        InfoBox.prototype.getCloseBoxImg_ = function() {
        
            var img = "";
        
            if (this.closeBoxURL_ !== "") {
        
                img = "<img";
                img += " src='" + this.closeBoxURL_ + "'";
                img += " align=right"; // Do this because Opera chokes on style='float: right;'
                img += " title='" + this.closeBoxTitle_ + "'";
                img += " style='";
                img += " position: relative;"; // Required by MSIE
                img += " cursor: pointer;";
                img += " margin: " + this.closeBoxMargin_ + ";";
                img += "'>";
            }
        
            return img;
        };
        
        /**
         * Adds the click handler to the InfoBox close box.
         * @private
         */
        InfoBox.prototype.addClickHandler_ = function() {
        
            var closeBox;
        
            if (this.closeBoxURL_ !== "") {
        
                closeBox = this.div_.firstChild;
                this.closeListener_ = google.maps.event.addDomListener(closeBox, "click", this.getCloseClickHandler_());
        
            } else {
        
                this.closeListener_ = null;
            }
        };
        
        /**
         * Returns the function to call when the user clicks the close box of an InfoBox.
         * @private
         */
        InfoBox.prototype.getCloseClickHandler_ = function() {
        
            var me = this;
        
            return function(e) {
        
                // 1.0.3 fix: Always prevent propagation of a close box click to the map:
                e.cancelBubble = true;
        
                if (e.stopPropagation) {
        
                    e.stopPropagation();
                }
        
                /**
                 * This event is fired when the InfoBox's close box is clicked.
                 * @name InfoBox#closeclick
                 * @event
                 */
                google.maps.event.trigger(me, "closeclick");
        
                me.close();
            };
        };
        
        /**
         * Pans the map so that the InfoBox appears entirely within the map's visible area.
         * @private
         */
        InfoBox.prototype.panBox_ = function(disablePan) {
        
            var map;
            var bounds;
            var xOffset = 0,
                yOffset = 0;
        
            if (!disablePan) {
        
                map = this.getMap();
        
                if (map instanceof google.maps.Map) { // Only pan if attached to map, not panorama
        
                    if (!map.getBounds().contains(this.position_)) {
                        // Marker not in visible area of map, so set center
                        // of map to the marker position first.
                        map.setCenter(this.position_);
                    }
        
                    var iwOffsetX = this.pixelOffset_.width;
                    var iwOffsetY = this.pixelOffset_.height;
                    var iwWidth = this.div_.offsetWidth;
                    var iwHeight = this.div_.offsetHeight;
                    var padX = this.infoBoxClearance_.width;
                    var padY = this.infoBoxClearance_.height;
        
                    if (map.panToBounds.length == 2) {
                        // Using projection.fromLatLngToContainerPixel to compute the infowindow position
                        // does not work correctly anymore for JS Maps API v3.32 and above if there is a
                        // previous synchronous call that causes the map to animate (e.g. setCenter when
                        // the position is not within bounds). Hence, we are using panToBounds with
                        // padding instead, which works synchronously.
                        var padding = {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        };
                        padding.left = -iwOffsetX + padX;
                        padding.right = iwOffsetX + iwWidth + padX;
                        if (this.alignBottom_) {
                            padding.top = -iwOffsetY + padY + iwHeight;
                            padding.bottom = iwOffsetY + padY;
                        } else {
                            padding.top = -iwOffsetY + padY;
                            padding.bottom = iwOffsetY + iwHeight + padY;
                        }
                        map.panToBounds(new google.maps.LatLngBounds(this.position_), padding);
                    } else {
                        var mapDiv = map.getDiv();
                        var mapWidth = mapDiv.offsetWidth;
                        var mapHeight = mapDiv.offsetHeight;
                        var pixPosition = this.getProjection().fromLatLngToContainerPixel(this.position_);
        
                        if (pixPosition.x < (-iwOffsetX + padX)) {
                            xOffset = pixPosition.x + iwOffsetX - padX;
                        } else if ((pixPosition.x + iwWidth + iwOffsetX + padX) > mapWidth) {
                            xOffset = pixPosition.x + iwWidth + iwOffsetX + padX - mapWidth;
                        }
                        if (this.alignBottom_) {
                            if (pixPosition.y < (-iwOffsetY + padY + iwHeight)) {
                                yOffset = pixPosition.y + iwOffsetY - padY - iwHeight;
                            } else if ((pixPosition.y + iwOffsetY + padY) > mapHeight) {
                                yOffset = pixPosition.y + iwOffsetY + padY - mapHeight;
                            }
                        } else {
                            if (pixPosition.y < (-iwOffsetY + padY)) {
                                yOffset = pixPosition.y + iwOffsetY - padY;
                            } else if ((pixPosition.y + iwHeight + iwOffsetY + padY) > mapHeight) {
                                yOffset = pixPosition.y + iwHeight + iwOffsetY + padY - mapHeight;
                            }
                        }
        
                        if (!(xOffset === 0 && yOffset === 0)) {
        
                            // Move the map to the shifted center.
                            //
                            var c = map.getCenter();
                            map.panBy(xOffset, yOffset);
                        }
                    }
                }
            }
        };
        
        /**
         * Sets the style of the InfoBox by setting the style sheet and applying
         * other specific styles requested.
         * @private
         */
        InfoBox.prototype.setBoxStyle_ = function() {
        
            var i, boxStyle;
        
            if (this.div_) {
        
                // Apply style values from the style sheet defined in the boxClass parameter:
                this.div_.className = this.boxClass_;
        
                // Clear existing inline style values:
                this.div_.style.cssText = "";
        
                // Apply style values defined in the boxStyle parameter:
                boxStyle = this.boxStyle_;
                for (i in boxStyle) {
        
                    if (boxStyle.hasOwnProperty(i)) {
        
                        this.div_.style[i] = boxStyle[i];
                    }
                }
        
                // Fix for iOS disappearing InfoBox problem.
                // See http://stackoverflow.com/questions/9229535/google-maps-markers-disappear-at-certain-zoom-level-only-on-iphone-ipad
                // Required: use "matrix" technique to specify transforms in order to avoid this bug.
                if ((typeof this.div_.style.WebkitTransform === "undefined") || (this.div_.style.WebkitTransform.indexOf("translateZ") === -1 && this.div_.style.WebkitTransform.indexOf("matrix") === -1)) {
        
                    this.div_.style.WebkitTransform = "translateZ(0)";
                }
        
                // Fix up opacity style for benefit of MSIE:
                //
                if (typeof this.div_.style.opacity !== "undefined" && this.div_.style.opacity !== "") {
                    // See http://www.quirksmode.org/css/opacity.html
                    this.div_.style.MsFilter = "\"progid:DXImageTransform.Microsoft.Alpha(Opacity=" + (this.div_.style.opacity * 100) + ")\"";
                    this.div_.style.filter = "alpha(opacity=" + (this.div_.style.opacity * 100) + ")";
                }
        
                // Apply required styles:
                //
                this.div_.style.position = "absolute";
                this.div_.style.visibility = 'hidden';
                if (this.zIndex_ !== null) {
        
                    this.div_.style.zIndex = this.zIndex_;
                }
            }
        };
        
        /**
         * Get the widths of the borders of the InfoBox.
         * @private
         * @return {Object} widths object (top, bottom left, right)
         */
        InfoBox.prototype.getBoxWidths_ = function() {
        
            var computedStyle;
            var bw = {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0
            };
            var box = this.div_;
        
            if (document.defaultView && document.defaultView.getComputedStyle) {
        
                computedStyle = box.ownerDocument.defaultView.getComputedStyle(box, "");
        
                if (computedStyle) {
        
                    // The computed styles are always in pixel units (good!)
                    bw.top = parseInt(computedStyle.borderTopWidth, 10) || 0;
                    bw.bottom = parseInt(computedStyle.borderBottomWidth, 10) || 0;
                    bw.left = parseInt(computedStyle.borderLeftWidth, 10) || 0;
                    bw.right = parseInt(computedStyle.borderRightWidth, 10) || 0;
                }
        
            } else if (document.documentElement.currentStyle) { // MSIE
        
                if (box.currentStyle) {
        
                    // The current styles may not be in pixel units, but assume they are (bad!)
                    bw.top = parseInt(box.currentStyle.borderTopWidth, 10) || 0;
                    bw.bottom = parseInt(box.currentStyle.borderBottomWidth, 10) || 0;
                    bw.left = parseInt(box.currentStyle.borderLeftWidth, 10) || 0;
                    bw.right = parseInt(box.currentStyle.borderRightWidth, 10) || 0;
                }
            }
        
            return bw;
        };
        
        /**
         * Invoked when <tt>close</tt> is called. Do not call it directly.
         */
        InfoBox.prototype.onRemove = function() {
        
            if (this.div_) {
        
                this.div_.parentNode.removeChild(this.div_);
                this.div_ = null;
            }
        };
        
        /**
         * Draws the InfoBox based on the current map projection and zoom level.
         */
        InfoBox.prototype.draw = function() {
        
            this.createInfoBoxDiv_();
        
            var pixPosition = this.getProjection().fromLatLngToDivPixel(this.position_);
        
            this.div_.style.left = (pixPosition.x + this.pixelOffset_.width) + "px";
        
            if (this.alignBottom_) {
                this.div_.style.bottom = -(pixPosition.y + this.pixelOffset_.height) + "px";
            } else {
                this.div_.style.top = (pixPosition.y + this.pixelOffset_.height) + "px";
            }
        
            if (this.isHidden_) {
        
                this.div_.style.visibility = "hidden";
        
            } else {
        
                this.div_.style.visibility = "visible";
            }
        };
        
        /**
         * Sets the options for the InfoBox. Note that changes to the <tt>maxWidth</tt>,
         *  <tt>closeBoxMargin</tt>, <tt>closeBoxTitle</tt>, <tt>closeBoxURL</tt>, and
         *  <tt>enableEventPropagation</tt> properties have no affect until the current
         *  InfoBox is <tt>close</tt>d and a new one is <tt>open</tt>ed.
         * @param {InfoBoxOptions} opt_opts
         */
        InfoBox.prototype.setOptions = function(opt_opts) {
            if (typeof opt_opts.boxClass !== "undefined") { // Must be first
        
                this.boxClass_ = opt_opts.boxClass;
                this.setBoxStyle_();
            }
            if (typeof opt_opts.boxStyle !== "undefined") { // Must be second
        
                this.boxStyle_ = opt_opts.boxStyle;
                this.setBoxStyle_();
            }
            if (typeof opt_opts.content !== "undefined") {
        
                this.setContent(opt_opts.content);
            }
            if (typeof opt_opts.disableAutoPan !== "undefined") {
        
                this.disableAutoPan_ = opt_opts.disableAutoPan;
            }
            if (typeof opt_opts.maxWidth !== "undefined") {
        
                this.maxWidth_ = opt_opts.maxWidth;
            }
            if (typeof opt_opts.pixelOffset !== "undefined") {
        
                this.pixelOffset_ = opt_opts.pixelOffset;
            }
            if (typeof opt_opts.alignBottom !== "undefined") {
        
                this.alignBottom_ = opt_opts.alignBottom;
            }
            if (typeof opt_opts.position !== "undefined") {
        
                this.setPosition(opt_opts.position);
            }
            if (typeof opt_opts.zIndex !== "undefined") {
        
                this.setZIndex(opt_opts.zIndex);
            }
            if (typeof opt_opts.closeBoxMargin !== "undefined") {
        
                this.closeBoxMargin_ = opt_opts.closeBoxMargin;
            }
            if (typeof opt_opts.closeBoxURL !== "undefined") {
        
                this.closeBoxURL_ = opt_opts.closeBoxURL;
            }
            if (typeof opt_opts.closeBoxTitle !== "undefined") {
        
                this.closeBoxTitle_ = opt_opts.closeBoxTitle;
            }
            if (typeof opt_opts.infoBoxClearance !== "undefined") {
        
                this.infoBoxClearance_ = opt_opts.infoBoxClearance;
            }
            if (typeof opt_opts.isHidden !== "undefined") {
        
                this.isHidden_ = opt_opts.isHidden;
            }
            if (typeof opt_opts.visible !== "undefined") {
        
                this.isHidden_ = !opt_opts.visible;
            }
            if (typeof opt_opts.enableEventPropagation !== "undefined") {
        
                this.enableEventPropagation_ = opt_opts.enableEventPropagation;
            }
        
            if (this.div_) {
        
                this.draw();
            }
        };
        
        /**
         * Sets the content of the InfoBox.
         *  The content can be plain text or an HTML DOM node.
         * @param {string|Node} content
         */
        InfoBox.prototype.setContent = function(content) {
            this.content_ = content;
        
            if (this.div_) {
        
                if (this.closeListener_) {
        
                    google.maps.event.removeListener(this.closeListener_);
                    this.closeListener_ = null;
                }
        
                // Odd code required to make things work with MSIE.
                //
                if (!this.fixedWidthSet_) {
        
                    this.div_.style.width = "";
                }
        
                if (typeof content.nodeType === "undefined") {
                    this.div_.innerHTML = this.getCloseBoxImg_() + content;
                } else {
                    this.div_.innerHTML = this.getCloseBoxImg_();
                    this.div_.appendChild(content);
                }
        
                // Perverse code required to make things work with MSIE.
                // (Ensures the close box does, in fact, float to the right.)
                //
                if (!this.fixedWidthSet_) {
                    this.div_.style.width = this.div_.offsetWidth + "px";
                    if (typeof content.nodeType === "undefined") {
                        this.div_.innerHTML = this.getCloseBoxImg_() + content;
                    } else {
                        this.div_.innerHTML = this.getCloseBoxImg_();
                        this.div_.appendChild(content);
                    }
                }
        
                this.addClickHandler_();
            }
        
            /**
             * This event is fired when the content of the InfoBox changes.
             * @name InfoBox#content_changed
             * @event
             */
            google.maps.event.trigger(this, "content_changed");
        };
        
        /**
         * Sets the geographic location of the InfoBox.
         * @param {LatLng} latlng
         */
        InfoBox.prototype.setPosition = function(latlng) {
        
            this.position_ = latlng;
        
            if (this.div_) {
        
                this.draw();
            }
        
            /**
             * This event is fired when the position of the InfoBox changes.
             * @name InfoBox#position_changed
             * @event
             */
            google.maps.event.trigger(this, "position_changed");
        };
        
        /**
         * Sets the zIndex style for the InfoBox.
         * @param {number} index
         */
        InfoBox.prototype.setZIndex = function(index) {
        
            this.zIndex_ = index;
        
            if (this.div_) {
        
                this.div_.style.zIndex = index;
            }
        
            /**
             * This event is fired when the zIndex of the InfoBox changes.
             * @name InfoBox#zindex_changed
             * @event
             */
            google.maps.event.trigger(this, "zindex_changed");
        };
        
        /**
         * Sets the visibility of the InfoBox.
         * @param {boolean} isVisible
         */
        InfoBox.prototype.setVisible = function(isVisible) {
        
            this.isHidden_ = !isVisible;
            if (this.div_) {
                this.div_.style.visibility = (this.isHidden_ ? "hidden" : "visible");
            }
        };
        
        /**
         * Returns the content of the InfoBox.
         * @returns {string}
         */
        InfoBox.prototype.getContent = function() {
        
            return this.content_;
        };
        
        /**
         * Returns the geographic location of the InfoBox.
         * @returns {LatLng}
         */
        InfoBox.prototype.getPosition = function() {
        
            return this.position_;
        };
        
        /**
         * Returns the zIndex for the InfoBox.
         * @returns {number}
         */
        InfoBox.prototype.getZIndex = function() {
        
            return this.zIndex_;
        };
        
        /**
         * Returns a flag indicating whether the InfoBox is visible.
         * @returns {boolean}
         */
        InfoBox.prototype.getVisible = function() {
        
            var isVisible;
        
            if ((typeof this.getMap() === "undefined") || (this.getMap() === null)) {
                isVisible = false;
            } else {
                isVisible = !this.isHidden_;
            }
            return isVisible;
        };
        
        /**
         * Returns the width of the InfoBox in pixels.
         * @returns {number}
         */
        InfoBox.prototype.getWidth = function() {
            var width = null;
        
            if (this.div_) {
                width = this.div_.offsetWidth;
            }
        
            return width;
        };
        
        /**
         * Returns the height of the InfoBox in pixels.
         * @returns {number}
         */
        InfoBox.prototype.getHeight = function() {
            var height = null;
        
            if (this.div_) {
                height = this.div_.offsetHeight;
            }
        
            return height;
        };
        
        /**
         * Shows the InfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
         */
        InfoBox.prototype.show = function() {
        
            this.isHidden_ = false;
            if (this.div_) {
                this.div_.style.visibility = "visible";
            }
        };
        
        /**
         * Hides the InfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
         */
        InfoBox.prototype.hide = function() {
        
            this.isHidden_ = true;
            if (this.div_) {
                this.div_.style.visibility = "hidden";
            }
        };
        
        /**
         * Adds the InfoBox to the specified map or Street View panorama. If <tt>anchor</tt>
         *  (usually a <tt>google.maps.Marker</tt>) is specified, the position
         *  of the InfoBox is set to the position of the <tt>anchor</tt>. If the
         *  anchor is dragged to a new location, the InfoBox moves as well.
         * @param {Map|StreetViewPanorama} map
         * @param {MVCObject} [anchor]
         */
        InfoBox.prototype.open = function(map, anchor) {
        
            var me = this;
        
            if (anchor) {
        
                this.setPosition(anchor.getPosition()); // BUG FIX 2/17/2018: needed for v3.32
                this.moveListener_ = google.maps.event.addListener(anchor, "position_changed", function() {
                    me.setPosition(this.getPosition());
                });
            }
        
            this.setMap(map);
        
            if (this.div_) {
        
                this.panBox_(this.disableAutoPan_); // BUG FIX 2/17/2018: add missing parameter
            }
        };
        
        /**
         * Removes the InfoBox from the map.
         */
        InfoBox.prototype.close = function() {
        
            var i;
        
            if (this.closeListener_) {
        
                google.maps.event.removeListener(this.closeListener_);
                this.closeListener_ = null;
            }
        
            if (this.eventListeners_) {
        
                for (i = 0; i < this.eventListeners_.length; i++) {
        
                    google.maps.event.removeListener(this.eventListeners_[i]);
                }
                this.eventListeners_ = null;
            }
        
            if (this.moveListener_) {
        
                google.maps.event.removeListener(this.moveListener_);
                this.moveListener_ = null;
            }
        
            if (this.contextListener_) {
        
                google.maps.event.removeListener(this.contextListener_);
                this.contextListener_ = null;
            }
        
            this.setMap(null);
        };

        return new InfoBox();
        
        }
    function GoogleMaps(element, map_data) {
        var options;
        this.element = element;
        map_data = JSON.parse(atob(map_data));
        this.map_data = $.extend({}, {}, map_data);
        options = this.map_data.map_options;
        this.settings = $.extend({
            "zoom": "5",
            "map_type_id": "ROADMAP",
            "scroll_wheel": true,
            "map_visual_refresh": false,
            "full_screen_control": false,
            "full_screen_control_position": "BOTTOM_RIGHT",
            "zoom_control": true,
            "zoom_control_style": "SMALL",
            "zoom_control_position": "TOP_LEFT",
            "map_type_control": true,
            "map_type_control_style": "HORIZONTAL_BAR",
            "map_type_control_position": "RIGHT_TOP",
            "scale_control": true,
            "street_view_control": true,
            "street_view_control_position": "TOP_LEFT",
            "overview_map_control": true,
            "center_lat": "40.6153983",
            "center_lng": "-74.2535216",
            "draggable": true
        }, {}, options);
        this.container = $("div[rel='" + $(this.element).attr("id") + "']");
        $(document).find(".wpgmp_map_container_placeholder").remove();
        $(document).find(".wpgmp_hide_map_container").removeClass("wpgmp_hide_map_container");

        var suppress_markers = false;
        if (this.map_data.map_tabs && this.map_data.map_tabs.direction_tab) {
            suppress_markers = this.map_data.map_tabs.direction_tab.suppress_markers;
        }
        this.directionsService = new google.maps.DirectionsService();
        this.directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: suppress_markers,
        });
        this.drawingmanager = {};
        this.geocoder = new google.maps.Geocoder();
        this.places = [];
        this.show_places = [];
        this.categories = {};
        this.tabs = [];
        this.all_shapes = [];
        this.wpgmp_polylines = [];
        this.wpgmp_polygons = [];
        this.wpgmp_circles = [];
        this.wpgmp_shape_events = [];
        this.wpgmp_rectangles = [];
        this.per_page_value = 0;
        this.current_amenities = [];
        this.route_directions = [];
        this.search_area = '';
        this.infobox = wpgmpInitializeInfoBox();
        this.markerClusterer = null;
        this.infowindow_marker = new google.maps.InfoWindow();
        this.init();
    }

    GoogleMaps.prototype = {

        init: function() {
            var map_obj = this;
            if (map_obj.map_data.map_property && map_obj.map_data.map_property.debug_mode == true) {
                console.log('*********WPGMP Debug Mode Output*********');
                console.log('Map ID =' + map_obj.map_data.map_property.map_id);
                if (map_obj.map_data.places) {
                    console.log('Total Locations=' + map_obj.map_data.places.length);
                }
                console.log('WPGMP Object=');
                console.log(map_obj.map_data);
                console.log('*********WPGMP Debug Mode End Output*********');
            }
            var center = new google.maps.LatLng(map_obj.settings.center_lat, map_obj.settings.center_lng);
            map_obj.map = new google.maps.Map(map_obj.element, {
                zoom: parseInt(map_obj.settings.zoom),
                center: center,
                disableDoubleClickZoom: (map_obj.settings.scroll_wheel != 'false'),
                scrollwheel: map_obj.settings.scroll_wheel,
                zoomControl: (map_obj.settings.zoom_control === true),
                fullscreenControl: (map_obj.settings.full_screen_control === true),
                fullscreenControlOptions: {
                    position: eval("google.maps.ControlPosition." + map_obj.settings.full_screen_control_position)
                },
                zoomControlOptions: {
                    style: eval("google.maps.ZoomControlStyle." + map_obj.settings.zoom_control_style),
                    position: eval("google.maps.ControlPosition." + map_obj.settings.zoom_control_position)
                },
                mapTypeControl: (map_obj.settings.map_type_control == true),
                mapTypeControlOptions: {
                    style: eval("google.maps.MapTypeControlStyle." + map_obj.settings.map_type_control_style),
                    position: eval("google.maps.ControlPosition." + map_obj.settings.map_type_control_position)
                },
                scaleControl: (map_obj.settings.scale_control == true),
                streetViewControl: (map_obj.settings.street_view_control == true),
                streetViewControlOptions: {
                    position: eval("google.maps.ControlPosition." + map_obj.settings.street_view_control_position)
                },
                overviewMapControl: (map_obj.settings.overview_map_control == true),
                overviewMapControlOptions: {
                    opened: map_obj.settings.overview_map_control
                },
                draggable: map_obj.settings.draggable,
                mapTypeId: eval("google.maps.MapTypeId." + map_obj.settings.map_type_id),
                styles: eval(map_obj.map_data.styles)
            });

            map_obj.map_loaded();
            map_obj.responsive_map();
            map_obj.create_markers();
            map_obj.display_markers();
            if (typeof map_obj.settings.google_fonts !== 'undefined') {
                map_obj.load_google_fonts(map_obj.settings.google_fonts);
            }
            
            if ( map_obj.map_data.street_view ) {
                map_obj.set_streetview(center);
            }

            if ( map_obj.map_data.bicyle_layer) {
                map_obj.set_bicyle_layer();
            }

            if ( map_obj.map_data.traffic_layer) {
                map_obj.set_traffic_layer();
            }

            if (map_obj.map_data.transit_layer) {
                map_obj.set_transit_layer();
            }

            if ( map_obj.map_data.panoramio_layer ) {
                map_obj.set_panoramic_layer();
            }

            if (map_obj.settings.display_45_imagery == '45') {
                map_obj.set_45_imagery();
            }

            if (typeof map_obj.map_data.map_visual_refresh === true) {
                map_obj.set_visual_refresh();
            }     


            if (map_obj.map_data.marker_cluster) {
                map_obj.set_marker_cluster();
            }      
       
            if (map_obj.settings.search_control == true) {
                map_obj.show_search_control();
            }
                 
            if (map_obj.map_data.listing) {

                    $(map_obj.container).on('click', '.categories_filter_reset_btn', function() {

                        $(map_obj.container).find('.wpgmp_filter_wrappers select').each(function() {
                            $(this).find('option:first').attr('selected', 'selected');
                        });
                        $('.wpgmp_search_input').val('');
                        map_obj.update_filters();
                        
                    });


                    $(map_obj.container).on('change', '[data-filter="dropdown"]', function() {
                        map_obj.update_filters();
                    });

                    $(map_obj.container).on('click', '[data-filter="checklist"]', function() {
                        map_obj.update_filters();
                    });

                    $(map_obj.container).on('click', '[data-filter="list"]', function() {

                        if ($(this).hasClass('fc_selected')) {
                            $(this).removeClass('fc_selected');
                        } else {
                            $(this).addClass('fc_selected');
                        }

                        map_obj.update_filters();
                    });
                    
                    map_obj.display_filters_listing();

                    $.each(map_obj.map_data.listing.filters, function(key, filter) {

                        $(map_obj.container).find('select[name="' + filter + '"]').on('change', function() {
                            map_obj.update_filters();
                        });

                    });                    

                    $(map_obj.container).find('[data-name="radius"]').on('change', function() {

                        var search_data = $(map_obj.container).find('[data-input="wpgmp-search-text"]').val();
                        if (search_data.length >= 2 && $(this).val() != '') {
                            map_obj.geocoder.geocode({
                                "address": search_data
                            }, function(results, status) {

                                if (status == google.maps.GeocoderStatus.OK) {
                                    map_obj.search_area = results[0].geometry.location;
                                    map_obj.update_filters();
                                }

                            });
                        } else {
                            map_obj.search_area = '';
                            map_obj.update_filters();
                        }


                    });

                    $(map_obj.container).find('[data-filter="map-perpage-location-sorting"]').on('change', function() {

                        map_obj.per_page_value = $(this).val();
                        map_obj.update_filters();

                    });

                    $(map_obj.container).find('[data-input="wpgmp-search-text"]').on('keyup', function() {
                        var search_data = $(this).val();
                        $(map_obj.container).find('[data-filter="map-radius"]').val('');
                        map_obj.search_area = '';
                        // Apply default radius
                        if (search_data.length >= 2 && map_obj.map_data.listing.apply_default_radius == true) {
                            if (search_data.length >= 2) {
                                map_obj.geocoder.geocode({
                                    "address": search_data
                                }, function(results, status) {

                                    if (status == google.maps.GeocoderStatus.OK) {
                                        map_obj.search_area = results[0].geometry.location;
                                        map_obj.update_filters();
                                    }

                                });
                            }

                        } else {
                            map_obj.update_filters();
                        }


                    });

            }
                
            $("body").on("click", ".wpgmp_marker_link", function() {
                map_obj.open_infowindow($(this).data("marker"));
                $('html, body').animate({
                    scrollTop: $(map_obj.container).offset().top - 150
                }, 500);
            });

            $(map_obj.container).on("click", "a[data-marker]", function() {
                map_obj.open_infowindow($(this).data("marker"));
                $('html, body').animate({
                    scrollTop: $(map_obj.container).offset().top - 150
                }, 500);
            });

            $(map_obj.container).on("click", "a[data-marker]", function() {
                map_obj.open_infowindow($(this).data("marker"));
            });

            // REGISTER AUTO SUGGEST
            map_obj.google_auto_suggest($(".wpgmp_auto_suggest"));

            if (map_obj.settings.fit_bounds === true) {
                map_obj.fit_bounds();
            }

        },
        load_google_fonts: function(fonts) {
            if (fonts && fonts.length > 0) {
                $.each(fonts, function(k, font) {
                    if (font.indexOf(',') >= 0) {
                        font = font.split(",");
                        font = font[0];
                    }
                    if (font.indexOf('"') >= 0) {
                        font = font.replace('"', '');
                        font = font.replace('"', '');
                    }
                    WebFont.load({
                        google: {
                            families: [font]
                        }
                    });
                });
            }
        },

        createMarker: function(place) {
            var map_obj = this;
            var map = map_obj.map;
            var placeLoc = place.geometry.location;
            var image = {
                url: place.icon,
                size: new google.maps.Size(25, 25),
                scaledSize: new google.maps.Size(25, 25)
            };

            place.marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location,
                icon: image
            });

            google.maps.event.addListener(place.marker, 'click', function() {
                 if (map_obj.settings.map_infowindow_customisations === true)
                    map_obj.amenity_infowindow.setContent('<div class="wpgmp_infowindow"><div class="wpgmp_iw_content">' + place.name + '</div></div>');
                else
                    map_obj.amenity_infowindow.setContent(place.name);
                map_obj.amenity_infowindow.open(map, this);
            });
            map_obj.current_amenities.push(place);
        },
       
        marker_bind: function(marker) {

            var map_obj = this;

            google.maps.event.addListener(marker, 'drag', function() {

                var position = marker.getPosition();

                map_obj.geocoder.geocode({
                    latLng: position
                }, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {

                        $("#googlemap_address").val(results[0].formatted_address);

                        $(".google_city").val(map_obj.wpgmp_finddata(results[0], 'administrative_area_level_3') || map_obj.wpgmp_finddata(results[0], 'locality'));
                        $(".google_state").val(map_obj.wpgmp_finddata(results[0], "administrative_area_level_1"));
                        $(".google_country").val(map_obj.wpgmp_finddata(results[0], "country"));

                        if (results[0].address_components) {
                            for (var i = 0; i < results[0].address_components.length; i++) {
                                for (var j = 0; j < results[0].address_components[i].types.length; j++) {
                                    if (results[0].address_components[i].types[j] == "postal_code") {
                                        var wpgmp_zip_code = results[0].address_components[i].long_name;
                                        $(".google_postal_code").val(wpgmp_zip_code);
                                    }
                                }
                            }
                        }
                    }
                });

                $(".google_latitude").val(position.lat());
                $(".google_longitude").val(position.lng());
            });

        },

        google_auto_suggest: function(obj) {

            var map_obj = this;

            obj.each(function() {
                var current_input = this;
                var autocomplete = new google.maps.places.Autocomplete(this);

                autocomplete.bindTo('bounds', map_obj.map);

                if ($(this).attr("name") == 'location_address') {
                    var infowindow = map_obj.infowindow_marker;
                    var marker = new google.maps.Marker({
                        map: map_obj.map,
                        draggable: true,
                        anchorPoint: new google.maps.Point(0, -29)
                    });

                    map_obj.marker_bind(marker);

                    google.maps.event.addListener(autocomplete, 'place_changed', function() {

                        var place = autocomplete.getPlace();
                        if (!place.geometry) {
                            return;
                        }

                        // If the place has a geometry, then present it on a map.
                        if (place.geometry.viewport) {
                            map_obj.map.fitBounds(place.geometry.viewport);
                        } else {
                            map_obj.map.setCenter(place.geometry.location);
                            map_obj.map.setZoom(17);
                        }

                        $(".google_latitude").val(place.geometry.location.lat());
                        $(".google_longitude").val(place.geometry.location.lng());
                        $(".google_city").val(map_obj.wpgmp_finddata(place, 'administrative_area_level_3') || map_obj.wpgmp_finddata(place, 'locality'));
                        $(".google_state").val(map_obj.wpgmp_finddata(place, "administrative_area_level_1"));
                        $(".google_country").val(map_obj.wpgmp_finddata(place, "country"));
                        if (place.address_components) {
                            for (var i = 0; i < place.address_components.length; i++) {
                                for (var j = 0; j < place.address_components[i].types.length; j++) {
                                    if (place.address_components[i].types[j] == "postal_code") {
                                        var wpgmp_zip_code = place.address_components[i].long_name;
                                        $(".google_postal_code").val(wpgmp_zip_code);
                                    }
                                }
                            }
                        }

                        marker.setPosition(place.geometry.location);
                        marker.setVisible(true);
                    });
                } else {

                    google.maps.event.addListener(autocomplete, 'place_changed', function() {

                        var place = autocomplete.getPlace();
                        if (!place.geometry) {
                            return;
                        }

                        $().val(place.geometry.location.lat());
                        $(current_input).data('longitude', place.geometry.location.lng());
                        $(current_input).data('latitude', place.geometry.location.lat());

                    });
                }
            });
        },

        wpgmp_finddata: function(result, type) {
            var component_name = "";
            for (var i = 0; i < result.address_components.length; ++i) {
                var component = result.address_components[i];
                $.each(component.types, function(index, value) {
                    if (value == type) {
                        component_name = component.long_name;
                    }
                });


            }
            return component_name;
        },

        open_infowindow: function(current_place) {
            var map_obj = this;

            $.each(this.map_data.places, function(key, place) {
                if (parseInt(place.id) == parseInt(current_place) && place.marker.visible === true) {
                   map_obj.openInfoWindow(place);
                }
            });
        },

        place_info: function(place_id) {

            var place_obj;

            $.each(this.places, function(index, place) {

                if (parseInt(place.id) == parseInt(place_id)) {
                    place_obj = place;
                }
            });

            return place_obj;
        },

        event_listener: function(obj, type, func) {
            google.maps.event.addListener(obj, type, func);
        },

        set_visual_refresh: function() {

            google.maps.visualRefresh = true;
        },

        set_45_imagery: function() {
            this.map.setTilt(45);
        },

        set_bicyle_layer: function() {

            var bikeLayer = new google.maps.BicyclingLayer();
            bikeLayer.setMap(this.map);
        },

        set_traffic_layer: function() {

            var traffic_layer = new google.maps.TrafficLayer();
            traffic_layer.setMap(this.map);
        },

        set_panoramic_layer: function() {

            var panoramic_layer = new google.maps.panoramio.PanoramioLayer();
            panoramic_layer.setMap(this.map);
        },

        set_transit_layer: function() {

            var transit_layer = new google.maps.TransitLayer();
            transit_layer.setMap(this.map);
        },

        set_streetview: function(latlng) {

            var panoOptions = {
                position: latlng,
                addressControlOptions: {
                    position: google.maps.ControlPosition.BOTTOM_CENTER
                },
                linksControl: this.map_data.street_view.links_control,
                panControl: this.map_data.street_view.street_view_pan_control,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                },
                enableCloseButton: this.map_data.street_view.street_view_close_button
            };
            if (this.map_data.street_view.pov_heading && this.map_data.street_view.pov_pitch) {
                panoOptions['pov'] = {
                    heading: parseInt(this.map_data.street_view.pov_heading),
                    pitch: parseInt(this.map_data.street_view.pov_pitch)
                };
            }
            var panorama = new google.maps.StreetViewPanorama(this.element, panoOptions);
        },

        set_marker_cluster: function() {
            var map_obj = this;
            var markers = [];
            var clusterStyles = [{
                textColor: 'black',
                url: map_obj.map_data.marker_cluster.icon,
                height: 32,
                width: 33
            }];
            $.each(this.places, function(index, place) {
                if (place.marker.visible == true) {
                    markers.push(place.marker);
                }

            });

            if (map_obj.map_data.marker_cluster.apply_style === true) {
                if (!map_obj.markerClusterer) {
                    map_obj.markerClusterer = new MarkerClusterer(map_obj.map, {}, {
                        gridSize: parseInt(map_obj.map_data.marker_cluster.grid),
                        maxZoom: parseInt(map_obj.map_data.marker_cluster.max_zoom),
                        styles: clusterStyles

                    });
                }

                map_obj.markerClusterer.clearMarkers();
                map_obj.markerClusterer.addMarkers(markers);

                google.maps.event.addListener(map_obj.markerClusterer, 'mouseover', function(c) {

                    c.clusterIcon_.div_.firstChild.src = map_obj.map_data.marker_cluster.hover_icon
                });

                google.maps.event.addListener(map_obj.markerClusterer, 'mouseout', function(c) {
                    c.clusterIcon_.div_.firstChild.src = map_obj.map_data.marker_cluster.icon
                });

            } else {
                if (!map_obj.markerClusterer) {
                    map_obj.markerClusterer = new MarkerClusterer(map_obj.map, {}, {
                        gridSize: parseInt(map_obj.map_data.marker_cluster.grid),
                        maxZoom: parseInt(map_obj.map_data.marker_cluster.max_zoom),
                        imagePath: map_obj.map_data.marker_cluster.image_path,
                    });
                }

                map_obj.markerClusterer.clearMarkers();
                map_obj.markerClusterer.addMarkers(markers);


            }

        },

        sortByPlace: function(order_by, data_type) {

            return function(a, b) {

                if (b[order_by] && a[order_by]) {

                    if (a[order_by] && b[order_by]) {
                        var a_val = a[order_by].toLowerCase();
                        var b_val = b[order_by].toLowerCase();
                        if (data_type == 'num') {
                            a_val = parseInt(a_val);
                            b_val = parseInt(b_val);
                        }
                        return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));
                    }

                }
            }

        },       

        sort_object_by_keyvalue: function(options, by, type, in_order) {

            var sortable = [];
            for (var key in options) {
                sortable.push(options[key]);
            }

            sortable.sort(this.sortByPlace(by, type));

            if (in_order == 'desc') {
                sortable.reverse();
            }

            return sortable;
        },     

        create_filters: function() {
            var map_obj = this;
            var options = '';
            var filters = {};
            var places = this.map_data.places;
            var wpgmp_listing_filter = this.map_data.listing;

            $.each(places, function(index, place) {
                if (typeof place.categories == 'undefined') {
                 place.categories = {}; 
                }

                $.each(place.categories, function(cat_index, category) {
                    if (typeof filters[category.type] == 'undefined') {
                        filters[category.type] = {};
                    }

                    if (category.name) { 
                        if (category.extension_fields && category.extension_fields.cat_order) {

                            filters[category.type][category.name] = {
                                'id': category.id,
                                'order': category.extension_fields.cat_order,
                                'name': category.name
                            };
                        } else { 
                            filters[category.type][category.name] = {
                            'id': category.id,
                            'order': 0,
                            'name': category.name
                            };
                        }
                    }

                });
            });
            // now create select boxes

            var content = '',
            by = 'name',
            type = '',
            inorder = 'asc';

            if (map_obj.map_data.listing) {  
                if (map_obj.map_data.listing.default_sorting) {  
                    if (map_obj.map_data.listing.default_sorting.orderby == 'listorder') {
                        by = 'order';
                        type = 'num';
                        inorder = map_obj.map_data.listing.default_sorting.inorder;
                    }
                  inorder = map_obj.map_data.listing.default_sorting.inorder;
                }
            }

            $.each(filters, function(index, options) {  
                if (wpgmp_listing_filter.display_category_filter === true && index == "category") {
                    content += '<select data-filter="dropdown" data-name="category" name="place_' + index + '">';
                    content += '<option value="">'+ wpgmp_local.select_category +'</option>';
                    options = map_obj.sort_object_by_keyvalue(options, by, type, inorder);
                    $.each(options, function(name, value) {
                         content += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    content += '</select>';

                }

            });

            return content;
        },

        update_filters: function() {
            var map_obj = this;
            var filters = {};

            var all_dropdowns = $(map_obj.container).find('[data-filter="dropdown"]');
            var all_checkboxes = $(map_obj.container).find('[data-filter="checklist"]:checked');
            var all_list = $(map_obj.container).find('[data-filter="list"].fc_selected');

            $.each(all_dropdowns, function(index, element) {
                if ($(this).val() != '') {

                    if (typeof filters[$(this).data('name')] == 'undefined') {
                        filters[$(this).data('name')] = [];
                    }

                    filters[$(this).data('name')].push($(this).val());
                }

            });

            $.each(all_checkboxes, function(index, element) {

                if (typeof filters[$(this).data('name')] == 'undefined') {
                    filters[$(this).data('name')] = [];
                }

                filters[$(this).data('name')].push($(this).val());

            });

            $.each(all_list, function(index, element) {

                if (typeof filters[$(this).data('name')] == 'undefined') {
                    filters[$(this).data('name')] = [];
                }

                filters[$(this).data('name')].push($(this).data('value').toString());

            });
            this.apply_filters(filters);

        },
        
        apply_filters: function(filters) {

            var map_obj = this;
            var showAll = true;
            var show = true;
            map_obj.show_places = [];

            var enable_search_term = false;
            // Filter by search box.
            if ($(map_obj.container).find('[data-input="wpgmp-search-text"]').length > 0) {
                var search_term = $(map_obj.container).find('[data-input="wpgmp-search-text"]').val();
                search_term = search_term.toLowerCase();
                if (search_term.length > 0) {
                    enable_search_term = true;
                }
            }

            if (((map_obj.map_data.map_tabs && map_obj.map_data.map_tabs.category_tab && map_obj.map_data.map_tabs.category_tab.cat_tab === true) || $(map_obj.container).find('input[data-marker-category]').length > 0)) {
                var all_selected_category_sel = $(map_obj.container).find('input[data-marker-category]:checked');
                var all_selected_category = [];
                var all_not_selected_location = [];
                if (all_selected_category_sel.length > 0) {
                    $.each(all_selected_category_sel, function(index, selected_category) {
                        all_selected_category.push($(selected_category).data("marker-category"));
                        var all_not_selected_location_sel = $(selected_category).closest('[data-container="wpgmp-category-tab-item"]').find('input[data-marker-location]:not(:checked)');
                        if (all_not_selected_location_sel.length > 0) {
                            $.each(all_not_selected_location_sel, function(index, not_selected_location) {
                                all_not_selected_location.push($(not_selected_location).data("marker-location"));
                            });
                        }
                    });
                }
                var all_selected_location_sel = $(map_obj.container).find('[data-container="wpgmp-category-tab-item"]').find('input[data-marker-location]:checked');
                var all_selected_location = [];
                if (all_selected_location_sel.length > 0) {
                    $.each(all_selected_location_sel, function(index, selected_location) {
                        all_selected_location.push($(selected_location).data("marker-location"));
                    });
                }
            }



            if (typeof map_obj.map_data.places != 'undefined') {
                $.each(map_obj.map_data.places, function(place_key, place) {

                    show = true;

                    if (typeof filters != 'undefined') {
                        $.each(filters, function(filter_key, filter_values) {

                            var in_fields = false;

                            if ($.isArray(filter_values)) {

                                if (typeof place.categories != 'undefined' && filter_key == "category") {

                                    $.each(place.categories, function(cat_index, category) {
                                        if ($.inArray(category.id, filter_values) > -1) {
                                            in_fields = true;
                                        }
                                    });
                                }

                                if (typeof place[filter_key] != 'undefined') {
                                    if ($.inArray(place[filter_key], filter_values) > -1) {
                                        in_fields = true;
                                    }
                                } else if (typeof place.location[filter_key] != 'undefined') {
                                    if ($.inArray(place.location[filter_key], filter_values) > -1) {
                                        in_fields = true;

                                    }
                                } else if (place.location.extra_fields && typeof place.location.extra_fields[filter_key] != 'undefined') {

                                    var dropdown_value = filter_values[0];
                                    if (place.location.extra_fields[filter_key] && place.location.extra_fields[filter_key].indexOf(dropdown_value) > -1) {
                                        in_fields = true;
                                    } else if ($.inArray(place.location.extra_fields[filter_key], filter_values) > -1) {
                                        in_fields = true;
                                    }

                                }

                                if (in_fields == false)
                                    show = false;

                            } else {
                                filter_values.val = "";
                            }
                        });
                    }

                    //Apply Search Filter.
                    if (enable_search_term === true && show === true) {

                        if (place.title != undefined && place.title.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.content != undefined && place.content.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.location.lat.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.location.lng.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.address && place.address.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;
                        } else if (place.location.state && place.location.state.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.location.country && place.location.country.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.location.postal_code && place.location.postal_code.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;

                        } else if (place.location.city && place.location.city.toLowerCase().indexOf(search_term) >= 0) {
                            show = true;
                        } else if (typeof map_obj.search_area != 'undefined' && map_obj.search_area != '' && map_obj.wpgmp_within_radius(place, map_obj.search_area) === true) {
                            show = true;
                        } else {
                            show = false;
                        }

                        if (typeof place.location.extra_fields != 'undefined') {
                            $.each(place.location.extra_fields, function(field, value) {

                                if (value) {
                                    value = value.toString();
                                    if (value && value.toLowerCase().indexOf(search_term) >= 0)
                                        show = true;
                                }


                            });
                        }

                    }

                    //Exclude locations without category if location filters are choosed by user
                    if ((place.categories.length == undefined || place.categories.length == 'undefined') && all_selected_category && (all_selected_category.length > 0) && ($(map_obj.container).find('input[name="wpgmp_select_all"]').is(":checked") == false) && show) {
                        show = false;
                    }

                    // if checked category
                    if (all_selected_category && show != false && place.categories.length != undefined) {

                        var in_checked_category = false;

                        if (all_selected_category.length === 0) {
                            // means no any category selected so show those location without categories.
                            if (typeof place.categories != 'undefined') {
                                $.each(place.categories, function(cat_index, category) {
                                    if (category.id === '')
                                        in_checked_category = true;
                                });
                            }
                        } else {
                            if (typeof place.categories != 'undefined') {
                                $.each(place.categories, function(cat_index, category) {
                                    if (category.id === '')
                                        in_checked_category = true;
                                    else if ($.inArray(parseInt(category.id), all_selected_category) > -1) {
                                        in_checked_category = true;
                                        place.marker.setIcon(category.icon);
                                    }

                                });
                            }
                        }

                        //Hide unchecked  locations.
                        if (all_not_selected_location.length !== 0) {
                            if ($.inArray(parseInt(place.id), all_not_selected_location) > -1) {
                                in_checked_category = false;
                            }
                        }

                        //var checked_categories = $(map_obj.container).find('input[data-marker-category]:checked').length;
                        if (in_checked_category === false)
                            show = false;
                        else
                            show = true;



                        //Show Here checked location.
                        if (all_selected_location.length !== 0) {
                            if ($.inArray(parseInt(place.id), all_selected_location) > -1) {
                                show = true;
                            }
                        }

                    }


                    place.marker.visible = show;
                    place.marker.setVisible(show);
                    if (show == false) {
                        place.infowindow.close();
                    }
                    place.marker.setAnimation(null);
                    if (show === true)
                        map_obj.show_places.push(place);
                });
            }


            if (typeof map_obj.map_data.map_options.bound_map_after_filter !== typeof undefined &&
                map_obj.map_data.map_options.bound_map_after_filter === true) {

                var after_filter_bounds = new google.maps.LatLngBounds();

                for (var j = 0; j < map_obj.show_places.length; j++) {
                    var markerInResult = new google.maps.LatLng(map_obj.show_places[j]['location']['lat'], map_obj.show_places[j]['location']['lng']);
                    after_filter_bounds.extend(markerInResult);
                }

                map_obj.map.fitBounds(after_filter_bounds);

            }

            if (map_obj.map_data.listing) {

                if ($(map_obj.container).find('[data-filter="map-sorting"]').val()) {
                    var order_data = $(map_obj.container).find('[data-filter="map-sorting"]').val().split("__");
                    var data_type = '';
                    if (order_data[0] !== '' && order_data[1] !== '') {

                        if (typeof order_data[2] != 'undefined') {
                            data_type = order_data[2];
                        }
                        map_obj.sorting(order_data[0], order_data[1], data_type);
                    }
                } else {
                    if (map_obj.map_data.listing.default_sorting) {
                        var data_type = '';
                        if (map_obj.map_data.listing.default_sorting.orderby == 'listorder') {
                            data_type = 'num';
                        }
                        map_obj.sorting(map_obj.map_data.listing.default_sorting.orderby, map_obj.map_data.listing.default_sorting.inorder, data_type);
                    }
                }

            }

            if (map_obj.map_data.marker_cluster) {
                map_obj.set_marker_cluster();
            }

        },
         
        display_filters_listing: function() {

            if (this.map_data.listing) {
               
                var filter_content = '<div class="wpgmp_filter_wrappers">' + this.display_filters() + '</div>';
                $(this.container).find(".wpgmp_map_parent").after(filter_content);

            }

        },

        display_filters: function() {

            var hide_locations = this.map_data.listing.hide_locations;

            var content = '';
            content += '<div class="wpgmp_before_listing">' + this.map_data.listing.listing_header + '</div>';

            content += '<div class="categories_filter">' + this.create_filters() + '<div data-container="wpgmp-filters-container"></div>';
            content += '</div>';

            return content;
        },

        map_loaded: function() {

            var map_obj = this;

            var gmap = map_obj.map;

            google.maps.event.addListenerOnce(gmap, 'idle', function() {

                var center = gmap.getCenter();
                google.maps.event.trigger(gmap, 'resize');
                gmap.setCenter(center);

            });

            if (map_obj.settings.center_by_nearest === true) {
                map_obj.center_by_nearest();
            }
            if (map_obj.settings.close_infowindow_on_map_click === true) {
                google.maps.event.addListener(gmap, "click", function(event) {
                    $.each(map_obj.places, function(key, place) {
                        place.infowindow.close();
                        place.marker.setAnimation(null);
                    });
                });
            }

            //for infowindow skins
            google.maps.event.addListener(map_obj.infobox, 'domready', function() {
                var wpgmp_iwOuter = $(map_obj.container).find('.infoBox');

                if (wpgmp_iwOuter.find('.fc-infowindow-default').length == 0 && wpgmp_iwOuter.find('.fc-item-default').length == 0 && wpgmp_iwOuter.find('.wpgmp_infowindow').length > 0) {
                   
                    wpgmp_iwOuter.find('.wpgmp_infowindow').prepend('<div class="infowindow-close"></div>');
                    $('.infowindow-close').click(function(e) {
                        e.preventDefault();
                        $.each(map_obj.places, function(key, place) {
                            place.infowindow.close();
                            place.marker.setAnimation(null);
                        });
                    });

                    //accordian
                    $(wpgmp_iwOuter).on('click', ".fc-accordion-tab", function() {
                        if ($(this).hasClass('active')) {
                            $(this).removeClass('active');
                            var acc_child = $(this).next().removeClass('active');
                        } else {
                            $(".fc-accordion-tab").removeClass('active');
                            $(".fc-accordion dd").removeClass('active');
                            $(this).addClass('active');
                            var acc_child = $(this).next().addClass('active');
                        }
                    });
                    if (wpgmp_iwOuter.find('.fc-infowindow-fano').length == 0 && wpgmp_iwOuter.find('.fc-item-fano').length == 0) {
                        wpgmp_iwOuter.addClass('infoBoxTail');
                    } else {
                        wpgmp_iwOuter.removeClass('infoBoxTail');
                    }

                }
            });

            if (map_obj.settings.map_infowindow_customisations === true) {
                google.maps.event.addListener(map_obj.infowindow_marker, 'domready', function() {

                    var wpgmp_iwOuter = $(map_obj.container).find('.gm-style-iw');

                    wpgmp_iwOuter.parent().css({
                        'width': '0px',
                        'height': '0px'
                    });
                    var wpgmp_iwCloseBtn = wpgmp_iwOuter.next();
                    wpgmp_iwCloseBtn.css('display', 'none');

                    var wpgmp_iwBackground = wpgmp_iwOuter.prev();

                    wpgmp_iwBackground.children(':nth-child(2)').css({
                        'display': 'none'
                    });

                    wpgmp_iwBackground.children(':nth-child(3)').css({
                        'background-color': '#000;',
                    });

                    wpgmp_iwBackground.children(':nth-child(4)').css({
                        'display': 'none'
                    });
                    var height = wpgmp_iwOuter.outerHeight() ;
                    wpgmp_iwBackground.children(':nth-child(3)').css({
                        'top':(height+18)+'px'
                    });
                    wpgmp_iwBackground.children(':nth-child(1)').css({
                        'top':(height+10)+'px'
                    });
                    wpgmp_iwBackground.children(':nth-child(3)').find('div').children().css({
                        'box-shadow': map_obj.settings.infowindow_border_color + ' 0px 1px 6px',
                        'border': '1px solid ' + map_obj.settings.infowindow_border_color,
                        'border-top': '',
                        'z-index': '1',
                        'background-color': map_obj.settings.infowindow_bg_color
                    });
                    wpgmp_iwOuter.find('.wpgmp_infowindow').prepend('<div class="infowindow-close"></div>');
                    wpgmp_iwOuter.on('click', '.infowindow-close', function(event){
                        $.each(map_obj.places, function(key, place) {
                            place.infowindow.close();
                            place.marker.setAnimation(null);
                        });
                    });
                });
            }

        },

        resize_map: function() {
            var map_obj = this;
            var gmap = map_obj.map;
            var zoom = gmap.getZoom();
            var center = gmap.getCenter();
            google.maps.event.trigger(this.map, 'resize');
            gmap.setZoom(zoom);
            gmap.setCenter(center);
        },
        responsive_map: function() {

            var map_obj = this;

            var gmap = map_obj.map;

            window.addEventListener("resize", function() {

                var zoom = gmap.getZoom();
                var center = gmap.getCenter();
                google.maps.event.trigger(gmap, "resize");
                gmap.setZoom(zoom);
                gmap.setCenter(center);
                gmap.getBounds();

            } );

        },


        show_search_control: function() {
            var map_obj = this;
            var input = $(map_obj.container).find('[data-input="map-search-control"]')[0];

            if (input !== undefined) {
                var searchBox = new google.maps.places.Autocomplete(input);

                if (wpgmp_local.wpgmp_country_specific && wpgmp_local.wpgmp_country_specific == true) {
                    searchBox.setComponentRestrictions({
                        'country': wpgmp_local.wpgmp_countries
                    });
                }

                map_obj.map.controls[eval("google.maps.ControlPosition." + map_obj.settings.search_control_position)].push(input);
                searchBox.bindTo('bounds', map_obj.map);
                google.maps.event.addListener(searchBox, 'place_changed', function() {
                    var place = searchBox.getPlace();
                    map_obj.map.setCenter(place.geometry.location);
                    map_obj.map.setZoom(parseInt(map_obj.map_data.map_options.map_zoom_after_search));
               });
            }  
        },

        fit_bounds: function() {
            var map_obj = this;
            var places = map_obj.map_data.places;
            var bounds = new google.maps.LatLngBounds();

            if (places !== undefined) {
                places.forEach(function(place) {

                    if (place.location.lat && place.location.lng) {
                        bounds.extend(new google.maps.LatLng(
                            parseFloat(place.location.lat),
                            parseFloat(place.location.lng)
                        ));
                    }

                });
            }
            map_obj.map.fitBounds(bounds);

        },

        create_markers: function() {

            var map_obj = this;
            var places = map_obj.map_data.places;
            var temp_listing_placeholder;
            var replaceData;
            var remove_keys = [];
            
            $.each(places, function(key, place) {
				
                if (place.location.lat && place.location.lng) {
                    if (typeof place.categories == 'undefined') {
                        place.categories = {};
                    }
                    place.marker = new google.maps.Marker({
                        position: new google.maps.LatLng(
                            parseFloat(place.location.lat),
                            parseFloat(place.location.lng)
                        ),
                        icon: place.location.icon,
                        url: place.url,
                        draggable: place.location.draggable,
                        map: map_obj.map,
                        clickable: place.location.infowindow_disable,
                    });

                    if (map_obj.settings.infowindow_drop_animation === true) {
                        place.marker.setAnimation(google.maps.Animation.DROP);
                    }

                    if (map_obj.settings.infowindow_filter_only === true) {
                        place.marker.visible = false;
                        place.marker.setVisible(false);
                    }


                    // bind event to marker
                    if (map_obj.map_data.page == 'edit_location')
                        map_obj.marker_bind(place.marker);
                    var location_categories = [];
                    if (typeof place.categories != 'undefined') {
                        for (var cat in place.categories) {
                            location_categories.push(place.categories[cat].name);
                        }
                    }
                    var content = '';
                     // replace infowindow content.
                    var marker_image = '';
                   
                     if( place.source == 'post' ) { 
                        marker_image = place.location.extra_fields.post_featured_image; 
                    } else {
                        marker_image = place.location.marker_image;
                    }

                    var temp_listing_placeholder = '';
                    if( place.source == 'post' ) { 
                        temp_listing_placeholder = map_obj.settings.infowindow_geotags_setting;
                    } else {
                        temp_listing_placeholder = map_obj.settings.infowindow_setting;
                    }

                    var temp_listing_placeholder = '';
                    var post_info_class = 'fc-infowindow-';
                    if (place.source == 'post') {
                        temp_listing_placeholder = map_obj.settings.infowindow_geotags_setting;
                        post_info_class = 'wpgmp_infowindow_post fc-item-' + map_obj.settings.infowindow_post_skin.name;
                    } else {
                        temp_listing_placeholder = map_obj.settings.infowindow_setting;
                        if (map_obj.map_data.page != 'edit_location' && map_obj.settings.infowindow_skin)
                        post_info_class = 'fc-infowindow-' + map_obj.settings.infowindow_skin.name;
                    }

                    if( typeof temp_listing_placeholder == 'undefined' ) {
                                temp_listing_placeholder = place.content;
                    }

                        replaceData = {
                            "{marker_id}": place.id,
                            "{marker_title}": place.title,
                            "{marker_address}": place.address,
                            "{marker_latitude}": place.location.lat,
                            "{marker_longitude}": place.location.lng,
                            "{marker_city}": place.location.city,
                            "{marker_state}": place.location.state,
                            "{marker_country}": place.location.country,
                            "{marker_postal_code}": place.location.postal_code,
                            "{marker_zoom}": place.location.zoom,
                            "{marker_icon}": place.location.icon,
                            "{marker_category}": location_categories.join(','),
                            "{marker_message}": place.content,
                            "{marker_image}": marker_image,
                            "{get_directions_link}": 'http://www.google.com/maps/place/' + parseFloat(place.location.lat) + ',' + parseFloat(place.location.lng),
                        };

                        //Add extra fields of locations
                        if (typeof place.location.extra_fields != 'undefined') {
                            for (var extra in place.location.extra_fields) {
                                if (!place.location.extra_fields[extra]) {
                                    replaceData['{' + extra + '}'] = '';
                                } else {
                                    replaceData['{' + extra + '}'] = place.location.extra_fields[extra];
                                }
                            }
                        }
                        temp_listing_placeholder = temp_listing_placeholder.replace(/{#if (.*?)}([\s\S]*?){\/if}/g, function(match, p1, p2) {
                            return replaceData['{' + p1 + '}'] ? p2 : '';
                        });

                        temp_listing_placeholder = temp_listing_placeholder.replace(/{[^{}]+}/g, function(match) {
                            if (match in replaceData) {
                                return (replaceData[match]);
                            } else {
                                return ("");
                            }
                        });

                    content = temp_listing_placeholder;

                    if(map_obj.map_data.map_options.infowindow_skin == '' ){
                        var wpgmp_infowindow_class = '';
                        var wpgmp_iw_content = '';
                    }else if(map_obj.map_data.map_options.infowindow_skin && map_obj.map_data.map_options.infowindow_skin.name && map_obj.map_data.map_options.infowindow_skin.name == 'basic'){
                        var wpgmp_infowindow_class = '';
                        var wpgmp_iw_content = '';
                    }else{
                        var wpgmp_infowindow_class = 'class="wpgmp_infowindow '+post_info_class+'"';
                        var wpgmp_iw_content = 'class="wpgmp_iw_content"';
                    }
                  
                    
                    if (content === "") {
                        if (map_obj.settings.map_infowindow_customisations === true && map_obj.settings.show_infowindow_header === true)
                            content = '<div '+wpgmp_infowindow_class+'><div class="wpgmp_iw_head"><div class="wpgmp_iw_head_content">' + place.title + '</div></div><div class="wpgmp_iw_content">' + place.content + '</div></div>';
                        else
                            content = '<div '+wpgmp_infowindow_class+'><div '+wpgmp_iw_content+'>' + place.content + '</div></div>';
                    } else {
                        if (map_obj.settings.map_infowindow_customisations === true && map_obj.settings.show_infowindow_header === true)
                            content = '<div '+wpgmp_infowindow_class+'><div class="wpgmp_iw_head"><div class="wpgmp_iw_head_content">' + place.title + '</div></div><div class="wpgmp_iw_content">' + content + '</div></div>';
                        else
                            content = '<div '+wpgmp_infowindow_class+'><div '+wpgmp_iw_content+'>' + content + '</div></div>';
            
                    }
                    place.infowindow_data = content;
                    place.infowindow = map_obj.infowindow_marker;

                    if (place.location.infowindow_default_open === true) {
                        map_obj.openInfoWindow(place);
                    } else if (map_obj.settings.default_infowindow_open === true) {
                        map_obj.openInfoWindow(place);
                    }
                    var on_event = map_obj.settings.infowindow_open_event;
                    var bounce_on_event = map_obj.settings.infowindow_bounce_animation;
                    map_obj.event_listener(place.marker, on_event, function() {
                        $.each(map_obj.places, function(key, prev_place) {
                            prev_place.infowindow.close();
                            prev_place.marker.setAnimation(null);
                        });
                        map_obj.openInfoWindow(place);
                        if (bounce_on_event == 'click') {
                            map_obj.toggle_bounce(place.marker);
                        }
                    });
                    if (bounce_on_event == 'mouseover' && on_event != 'mouseover') {
                        map_obj.event_listener(place.marker, 'mouseover', function() {
                            place.marker.setAnimation(google.maps.Animation.BOUNCE);
                        });

                        map_obj.event_listener(place.marker, 'mouseout', function() {
                            place.marker.setAnimation(null);
                        });
                    }

                    if (bounce_on_event != '') {
                        google.maps.event.addListener(place.infowindow, 'closeclick', function() {
                            place.marker.setAnimation(null);
                        });
                    }

                    map_obj.places.push(place);
                } else {
                    remove_keys.push(key);
                }
            });
            $.each(remove_keys, function(index, value) {
                places.splice(value, 1);
            });

        },

        toggle_bounce: function(marker) {
            if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
        },
        display_markers: function() {

            var map_obj = this;
            map_obj.show_places = [];
            map_obj.categories = [];
            var categories = {};
            for (var i = 0; i < map_obj.places.length; i++) {
                map_obj.places[i].marker.setMap(map_obj.map);
                if (map_obj.places[i].marker.visible === true) {
                    map_obj.show_places.push(this.places[i]);
                }

                if (typeof map_obj.places[i].categories != 'undefined') {
                    $.each(map_obj.places[i].categories, function(index, category) {

                        if (typeof categories[category.name] == 'undefined') {
                            categories[category.name] = category;
                        }
                    });
                }
            }

            this.categories = categories;
        },

        get_current_location: function(success_func, error_func) {

            var map = this;

            if (typeof map.user_location == 'undefined') {

                navigator.geolocation.getCurrentPosition(function(position) {
                    map.user_location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                    if (success_func)
                        success_func(map.user_location);

                }, function(ErrorPosition) {

                    if (error_func)
                        error_func(ErrorPosition);

                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                if (success_func)
                    success_func(map.user_location);
            }
        },

        openInfoWindow: function(place) {
            var map_obj = this;
            var skin = 'default';
            if (place.source == 'post') {
                skin = map_obj.settings.infowindow_post_skin.name;
            } else if (map_obj.map_data.page != 'edit_location' && map_obj.settings.infowindow_skin) {
                skin = map_obj.settings.infowindow_skin.name;
            }

            if (skin != 'default' && skin != 'basic') {
                var infoboxText = document.createElement("div");
                infoboxText.className = 'wpgmp_infobox'
                
                var infoboxOptions = {
                    content: infoboxText,
                    disableAutoPan: false,
                    alignBottom: true,
                    maxWidth: 0,
                    pixelOffset: (skin == 'fano') ? new google.maps.Size(-150, -40) : new google.maps.Size(-150, -55),
                    zIndex: null,
                    boxStyle: {
                        width: "300px"
                    },
                    closeBoxMargin: "0",
                    closeBoxURL: "",
                    infoBoxClearance: new google.maps.Size(25, 25),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false,
                };
                
                place.infowindow = map_obj.infobox;

                place.infowindow.setOptions(infoboxOptions);
                infoboxText.innerHTML = place.infowindow_data;
            } else {
                place.infowindow = map_obj.infowindow_marker;
                place.infowindow.setContent(place.infowindow_data);
            }

            if (place.location.onclick_action == "post") {
                if (place.location.open_new_tab == 'yes')
                    window.open(place.location.redirect_permalink, '_blank');
                else
                    window.open(place.location.redirect_permalink, '_self');
            } else if (place.location.onclick_action == "custom_link") {
                if (place.location.open_new_tab == 'yes')
                    window.open(place.location.redirect_custom_link, '_blank');
                else
                    window.open(place.location.redirect_custom_link, '_self');
            } else {

                place.infowindow.open(this.map, place.marker);

                if (typeof map_obj.settings.infowindow_open_event != 'undefined' && map_obj.settings.infowindow_open_event == 'click' && typeof map_obj.settings.infowindow_click_change_center != 'undefined' && map_obj.settings.infowindow_click_change_center == true) {
                    map_obj.map.setCenter(place.marker.getPosition());
                }
                if (typeof map_obj.settings.infowindow_open_event != 'undefined' && map_obj.settings.infowindow_open_event == 'click' && typeof map_obj.settings.infowindow_click_change_zoom != 'undefined' && map_obj.settings.infowindow_click_change_zoom > 0) {
                    map_obj.map.setZoom(map_obj.settings.infowindow_click_change_zoom);
                }
                if (this.map_data.map_tabs && this.map_data.map_tabs.direction_tab && this.map_data.map_tabs.direction_tab.dir_tab === true) {
                    $(this.container).find('.start_point').val(place.address);
                }
            }
            $(map_obj.container).find(".wpgmp_extra_field:contains('wpgmp_empty')").remove();
            $(map_obj.container).find(".wpgmp_empty").prev().remove();
            $(map_obj.container).find(".wpgmp_empty").remove();

        },
    };

    $.fn.maps = function(options, places) {

        this.each(function() {

            if (!$.data(this, "wpgmp_maps")) {
                $.data(this, "wpgmp_maps", new GoogleMaps(this, options, places));
            }

        });
        // chain jQuery functions
        return this;
    };

}(jQuery, window, document));

function wpgmpInitMap() {

    jQuery(document).ready(function($) {

        if ($.fn.maps) {
            
            $(".wpgmp_map").each(function() {

                var mapId = $(this).data("map-id");
                var mapVarName = 'mapdata' + mapId;
                var mapData = window.wpgmp && window.wpgmp[mapVarName];
                if (mapData) {
                    $("#map" + mapId).maps(mapData).data("wpgmp_maps");
                } else {
                    console.warn("Map data missing for map : " + mapId);
                }

            });
        }
    });
}

