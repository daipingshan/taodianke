/*!
 * Layzr.js 1.4.2 - A small, fast, modern, and dependency-free library for lazy loading.
 * Copyright (c) 2015 Michael Cavalea - http://callmecavs.github.io/layzr.js/
 * License: MIT
 */

(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory();
    } else {
        root.Layzr = factory();
    }
}(this, function() {
    'use strict';

// CONSTRUCTOR

    function Layzr(options) {
        // debounce
        this._lastScroll = 0;
        this._ticking = false;

        // options
        options = options || {};
        try {
            this._optionsContainer = document.querySelector(options.container) || window;
        } catch (e) {

        }

        this._optionsSelector = options.selector || '[data-layzr]';
        this._optionsAttr = options.attr || 'data-layzr';
        this._optionsAttrRetina = options.retinaAttr || 'data-layzr-retina';
        this._optionsAttrBg = options.bgAttr || 'data-layzr-bg';
        this._optionsAttrHidden = options.hiddenAttr || 'data-layzr-hidden';
        this._optionsThreshold = options.threshold || 0;
        this._optionsCallback = options.callback || null;

        // properties
        this._retina = window.devicePixelRatio > 1;
        this._srcAttr = this._retina ? this._optionsAttrRetina : this._optionsAttr;


        try {
            // nodelist
            this._nodes = document.querySelectorAll(this._optionsSelector);
            // scroll and resize handler
            this._handlerBind = this._requestScroll.bind(this);
        } catch (e) {

        }




        // call to create
        this._create();
    }

// DEBOUNCE HELPERS
// adapted from: http://www.html5rocks.com/en/tutorials/speed/animations/

    Layzr.prototype._requestScroll = function() {
        if (this._optionsContainer === window) {
            this._lastScroll = window.pageYOffset;
        }
        else {
            this._lastScroll = this._optionsContainer.scrollTop + this._getOffset(this._optionsContainer);
        }

        this._requestTick();
    };

    Layzr.prototype._requestTick = function() {
        if (!this._ticking) {
            requestAnimationFrame(this.update.bind(this));
            this._ticking = true;
        }
    };

// OFFSET HELPER
// remember, getBoundingClientRect is relative to the viewport

    Layzr.prototype._getOffset = function(node) {
        return node.getBoundingClientRect().top + window.pageYOffset;
    };

// HEIGHT HELPER

    Layzr.prototype._getContainerHeight = function() {
        return this._optionsContainer.innerHeight
                || this._optionsContainer.offsetHeight;
    }

// LAYZR METHODS

    Layzr.prototype._create = function() {
        // fire scroll event once

        try {
            this._handlerBind();
             // bind scroll and resize event
            this._optionsContainer.addEventListener('scroll', this._handlerBind, false);
            this._optionsContainer.addEventListener('resize', this._handlerBind, false);
        } catch (e) {

        }

       
    };

    Layzr.prototype._destroy = function() {
        // unbind scroll and resize event
        this._optionsContainer.removeEventListener('scroll', this._handlerBind, false);
        this._optionsContainer.removeEventListener('resize', this._handlerBind, false);
    };

    Layzr.prototype._inViewport = function(node) {
        // get viewport top and bottom offset
        var viewportTop = this._lastScroll;
        var viewportBottom = viewportTop + this._getContainerHeight();

        // get node top and bottom offset
        var nodeTop = this._getOffset(node);
        var nodeBottom = nodeTop + this._getContainerHeight();

        // calculate threshold, convert percentage to pixel value
        var threshold = (this._optionsThreshold / 100) * window.innerHeight;

        // return if node in viewport
        return nodeBottom >= viewportTop - threshold
                && nodeTop <= viewportBottom + threshold
                && !node.hasAttribute(this._optionsAttrHidden);
    };

    Layzr.prototype._reveal = function(node) {
        // get node source
        var source = node.getAttribute(this._srcAttr) || node.getAttribute(this._optionsAttr);

        // set node src or bg image
        if (node.hasAttribute(this._optionsAttrBg)) {
            node.style.backgroundImage = 'url(' + source + ')';
        }
        else {
            node.setAttribute('src', source);
        }

        // call the callback
        if (typeof this._optionsCallback === 'function') {
            // "this" will be the node in the callback
            this._optionsCallback.call(node);
        }

        // remove node data attributes
        node.removeAttribute(this._optionsAttr);
        node.removeAttribute(this._optionsAttrRetina);
        node.removeAttribute(this._optionsAttrBg);
        node.removeAttribute(this._optionsAttrHidden);
    };

    Layzr.prototype.updateSelector = function() {
        // update cached list of nodes matching selector
        this._nodes = document.querySelectorAll(this._optionsSelector);
    };

    Layzr.prototype.update = function() {
        // cache nodelist length
        var nodesLength = this._nodes.length;

        // loop through nodes
        for (var i = 0; i < nodesLength; i++) {
            // cache node
            var node = this._nodes[i];

            // check if node has mandatory attribute
            if (node.hasAttribute(this._optionsAttr)) {
                // check if node in viewport
                if (this._inViewport(node)) {
                    // reveal node
                    this._reveal(node);
                }
            }
        }

        // allow for more animation frames
        this._ticking = false;
    };

    return Layzr;
}));
