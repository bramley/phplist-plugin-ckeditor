/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    config.title = false;
    config.browserContextMenuOnCtrl = false;
    CKEDITOR.disableAutoInline = true;

    config.docType = '<!DOCTYPE HTML>';
    config.emailProtection = '';
    config.enterMode = CKEDITOR.ENTER_DIV; // p is recommended but users....
    config.fillEmptyBlocks = true;
    config.fontSize_sizes = '8pt/ 8pt;9pt/ 9pt;10pt/10pt;11pt/11pt;12pt/12pt;14pt/14pt;18pt/18pt;24pt/24pt;36pt/36pt';
    config.font_names = 'Arial;Calibri;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana';
    config.ignoreEmptyParagraph = true;

    // toolbar can collapse or not, we allow this
    config.toolbarCanCollapse = true;
};

// HTML formattings
CKEDITOR.on('instanceReady', function (ev) {
    var element = ev.editor.element;
    var form = element.$.form && new CKEDITOR.dom.element(element.$.form);
    if (form) {

        var editor = ev.editor,
            dataProcessor = editor.dataProcessor,
            htmlFilter = dataProcessor && dataProcessor.htmlFilter;

        // set HTML 4 style
        SetHTMLFilterRules(htmlFilter);
    }
});

// HTML 4 style
function SetHTMLFilterRules(htmlFilter) {
    // Output properties as attributes, not styles.
    htmlFilter.addRules({
        elements: {
            $: function (element) {
                // Output dimensions of images as width and height
                if (element.name == 'img') {
                    var style = element.attributes.style;

                    if (style) {
                        // Get the width from the style.
                        var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style);
                        var width = match && match[1];

                        // Get the height from the style.
                        var match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
                        var height = match && match[1];

                        // Get the float from the style.
                        var match = /(?:^|\s)float\s*:\s*(\w*)(;|$)/i.exec(style);
                        var afloat = match && match[1];
                        if (afloat)
                            afloat = afloat.replace(/;/, "");
                        if (afloat != "right" && afloat != "left") {
                            afload = false;
                        }

                        if (width) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)width\s*:\s*(\d+)px;?/i, '');
                            element.attributes.width = width;
                        }

                        if (height) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)height\s*:\s*(\d+)px;?/i, '');
                            element.attributes.height = height;
                        }

                        if (afloat) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)float\s*:\s*(\w*);?/i, '');
                            element.attributes.align = afloat;
                        }

                    }
                }

                // Output alignment of paragraphs, div and td using align / valign
                if (element.name == 'p' || element.name == 'div' || element.name == 'td') {
                    var style = element.attributes.style;

                    if (style) {
                        // Get the align from the style.
                        var match = /(?:^|\s)text-align\s*:\s*(\w*)(;|$)/i.exec(style);
                        var align = match && match[1];
                        if (align)
                            align = align.replace(/;/, "");

                        // Get the valign from the style for td only.
                        var match = /(?:^|\s)vertical-align\s*:\s*(\w*)(;|$)/i.exec(style);
                        var valign = match && match[1];
                        if (valign)
                            valign = valign.replace(/;/, "");

                        if (align) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)text-align\s*:\s*(\w*)(;|$)?/i, '');
                            element.attributes.align = align;
                        }

                        if (valign) // for td only
                        {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)vertical-align\s*:\s*(\w*)(;|$)?/i, '');
                            element.attributes.valign = valign;
                        }

                    }
                }

                // Output image borders as HTML
                if (element.name == 'img') {
                    var style = element.attributes.style;

                    if (style) {
                        //margin *******************
                        var match = /(?:^|\s)margin\s*:\s*(.*)(;|$)/i.exec(style);
                        var margin = match && match[1];
                        if (margin)
                            margin = margin.replace(/;/, "");
                        if (margin) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)margin\s*:\s*(.*)(;|$)/i, '');
                            margin = margin.trim();
                            if (margin.indexOf(" ") != -1) { // e.g. margin 10px 20px;
                                var vspace = parseInt(margin);
                                var hspace = parseInt(margin.substr(margin.indexOf(" ") + 1));
                                element.attributes.hspace = hspace;
                                element.attributes.vspace = vspace;
                            } else if (parseInt(margin) > 0) { // e.g. margin 10px;
                                element.attributes.hspace = parseInt(margin);
                                element.attributes.vspace = parseInt(margin);
                            }
                        }

                        // chrome long style
                        var match = /(?:^|\s)margin-left\s*:\s*(\d+)px/i.exec(style);
                        var margin_left = match && match[1] && parseInt(match[1]) >= 0;
                        if (margin_left)
                            margin_left = parseInt(match[1]);
                        var match = /(?:^|\s)margin-right\s*:\s*(\d+)px/i.exec(style);
                        var margin_right = match && match[1] && parseInt(match[1]) >= 0;
                        if (margin_right)
                            margin_right = parseInt(match[1]);
                        var match = /(?:^|\s)margin-top\s*:\s*(\d+)px/i.exec(style);
                        var margin_top = match && match[1] && parseInt(match[1]) >= 0;
                        if (margin_top)
                            margin_top = parseInt(match[1]);
                        var match = /(?:^|\s)margin-bottom\s*:\s*(\d+)px/i.exec(style);
                        var margin_bottom = match && match[1] && parseInt(match[1]) >= 0;
                        if (margin_bottom)
                            margin_bottom = parseInt(match[1]);

                        if (margin_left && margin_right && (margin_left == margin_right)) {
                            element.attributes.hspace = margin_left;
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)margin-left\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)margin-right\s*:\s*(\d+)px(;|$)/i, '');
                        }

                        if (margin_top && margin_bottom && (margin_top == margin_bottom)) {
                            element.attributes.vspace = margin_top;
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)margin-top\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)margin-bottom\s*:\s*(\d+)px(;|$)/i, '');
                        }

                        // border *************************

                        // FF short style
                        // border-width.
                        var match = /(?:^|\s)border-width\s*:\s*(\d+)px/i.exec(style);
                        var border_width = match && match[1] && parseInt(match[1]) >= 0;
                        if (border_width)
                            border_width = parseInt(match[1]);
                        // border-style
                        var match = /(?:^|\s)border-style\s*:\s*(\w*)(;|$)/i.exec(style);
                        var border_style = match && match[1];
                        if (border_style)
                            border_style = border_style.replace(/;/, "");

                        // IE long style
                        var match = /(?:^|\s)border-bottom\s*:\s*(\S*)\s*solid/i.exec(style);
                        var border_bottom = match && match[1] && match[0].indexOf("solid") != -1 && parseInt(match[1]) >= 0;
                        if (border_bottom)
                            border_bottom = parseInt(match[1]);

                        var match = /(?:^|\s)border-left\s*:\s*(\S*)\s*solid/i.exec(style);
                        var border_left = match && match[1] && match[0].indexOf("solid") != -1 && parseInt(match[1]) >= 0;
                        if (border_left)
                            border_left = parseInt(match[1]);

                        var match = /(?:^|\s)border-right\s*:\s*(\S*)\s*solid/i.exec(style);
                        var border_right = match && match[1] && match[0].indexOf("solid") != -1 && parseInt(match[1]) >= 0;
                        if (border_right)
                            border_right = parseInt(match[1]);

                        var match = /(?:^|\s)border-top\s*:\s*(\S*)\s*solid/i.exec(style);
                        var border_top = match && match[1] && match[0].indexOf("solid") != -1 && parseInt(match[1]) >= 0;
                        if (border_top)
                            border_top = parseInt(match[1]);

                        if (border_bottom && border_left && border_right && border_top) {

                            var border = new Array(border_bottom, border_left, border_right, border_top);
                            var b = border[0];
                            for (var i = 1; i < border.length; i++)
                                if (b != border[i]) {
                                    b = -1;
                                    break;
                                }

                            if (b != -1) {
                                border_width = String(parseInt(border_bottom));
                                border_style = "solid";
                            }
                        }
                        // IE long style /

                        // Chrome long style
                        var match = /(?:^|\s)border-top-width\s*:\s*(\d+)px/i.exec(style);
                        var border_top = match && match[1] && parseInt(match[1]) >= 0;
                        if (border_top)
                            border_top = parseInt(match[1]);

                        var match = /(?:^|\s)border-right-width\s*:\s*(\d+)px/i.exec(style);
                        var border_right = match && match[1] && parseInt(match[1]) >= 0;
                        if (border_right)
                            border_right = parseInt(match[1]);

                        var match = /(?:^|\s)border-left-width\s*:\s*(\d+)px/i.exec(style);
                        var border_left = match && match[1] && parseInt(match[1]) >= 0;
                        if (border_left)
                            border_left = parseInt(match[1]);

                        var match = /(?:^|\s)border-bottom-width\s*:\s*(\d+)px/i.exec(style);
                        var border_bottom = match && match[1] && parseInt(match[1]) >= 0;
                        if (border_bottom)
                            border_bottom = parseInt(match[1]);

                        var match = /(?:^|\s)border-top-style\s*:\s*(\w*)(;|$)/i.exec(style);
                        var border_style_top = match && match[1] && match[1].lastIndexOf('solid') != -1;
                        var match = /(?:^|\s)border-right-style\s*:\s*(\w*)(;|$)/i.exec(style);
                        var border_style_right = match && match[1] && match[1].lastIndexOf('solid') != -1;
                        var match = /(?:^|\s)border-left-style\s*:\s*(\w*)(;|$)/i.exec(style);
                        var border_style_left = match && match[1] && match[1].lastIndexOf('solid') != -1;
                        var match = /(?:^|\s)border-bottom-style\s*:\s*(\w*)(;|$)/i.exec(style);
                        var border_style_bottom = match && match[1] && match[1].lastIndexOf('solid') != -1;

                        if (border_style_top && border_style_right && border_style_left && border_style_bottom)
                            border_style = "solid";


                        if (border_bottom && border_left && border_right && border_top) {

                            var border = new Array(border_bottom, border_left, border_right, border_top);
                            var b = border[0];
                            for (var i = 1; i < border.length; i++)
                                if (b != border[i]) {
                                    b = -1;
                                    break;
                                }

                            if (b != -1) {
                                border_width = String(parseInt(border_bottom));
                            }
                        }
                        // Chrome long style /


                        if (border_width >= 0 && border_style == "solid") {

                            // FF
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-width\s*:\s*(\d+)px?/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-style\s*:\s*(\w*)(;|$)?/i, '');

                            // IE long style with/without ;
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-bottom\s*:\s*(\S*)\s*solid(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-left\s*:\s*(\S*)\s*solid(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-right\s*:\s*(\S*)\s*solid(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-top\s*:\s*(\S*)\s*solid(;|$)/i, '');
                            // // IE long style with/without ; /

                            // Chrome long style
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-top-width\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-right-width\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-left-width\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-bottom-width\s*:\s*(\d+)px(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-top-style\s*:\s*(\w*)(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-right-style\s*:\s*(\w*)(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-left-style\s*:\s*(\w*)(;|$)/i, '');
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)border-bottom-style\s*:\s*(\w*)(;|$)/i, '');
                            // Chrome long style//

                            element.attributes.border = parseInt(border_width);
                        }
                    }
                }

                // output td background-color as bgcolor and white-spacing als nowrap
                if (element.name == 'table' || element.name == 'td') {
                    // IE bug <td align="middle"> is invalid must be <td align="center">
                    if (element.attributes.align && element.attributes.align == "middle")
                        element.attributes.align = "center";

                    var style = element.attributes.style;
                    if (style) {

                        style = convertRGBToHex(style); // FF rgb() problem
                        element.attributes.style = style;

                        // Get the width from the style.
                        var match = /(?:^|\s)width\s*:\s*(\S*)?/i.exec(style);
                        var width = match && match[1];
                        if (width && width.indexOf("%") == -1)
                            width = parseInt(width);

                        // Get the height from the style.
                        var match = /(?:^|\s)height\s*:\s*(\S*)?/i.exec(style);
                        var height = match && match[1];
                        if (height && height.indexOf("%") == -1)
                            height = parseInt(height);

                        if (width) {
                            width = String(width).replace(/;/, "");
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)width\s*:\s*(\S*)?/i, '');
                            element.attributes.width = width;
                        }

                        if (height) {
                            height = String(height).replace(/;/, "");
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)height\s*:\s*(\S*)?/i, '');
                            element.attributes.height = height;
                        }

                        // background-color
                        var match = /(?:^|\s)background-color\s*:\s*(\S*)(;|$)/i.exec(style);
                        var background_color = match && match[1];

                        if (background_color) {
                            // we let it in td tag, so webbased emailers can't override it --> element.attributes.style = element.attributes.style.replace( /(?:^|\s)background-color\s*:\s*(\S*)(;|$)?/i , '' );
                            background_color = background_color.replace(/;/, "");
                            element.attributes.bgcolor = background_color;
                        }

                        // white-space: nowrap
                        var match = /(?:^|\s)white-space\s*:\s*(\S*)(;|$)/i.exec(style);
                        var white_space = match && match[1];
                        if (white_space)
                            white_space = white_space.replace(/;/, "");
                        if (white_space != "nowrap")
                            white_space = false;
                        if (white_space) {
                            element.attributes.style = element.attributes.style.replace(/(?:^|\s)white-space\s*:\s*(\S*)(;|$)?/i, '');
                            element.attributes.nowrap = "nowrap";
                        }

                    }
                }

                // output body background-color as bgcolor
                if (element.name == 'body') {
                    var style = element.attributes.style;
                    if (style) {
                        style = convertRGBToHex(style); // FF rgb() problem
                        element.attributes.style = style;
                        // background-color
                        var match = /(?:^|\s)background-color\s*:\s*(\S*)(;|$)/i.exec(style);
                        var background_color = match && match[1];

                        if (background_color) {
                            // we let it in body tag, so webbased emailers can't override it --> element.attributes.style = element.attributes.style.replace( /(?:^|\s)background-color\s*:\s*(\S*)(;|$)?/i , '' );
                            background_color = background_color.replace(/;/, "");
                            element.attributes.bgcolor = background_color;
                        }

                    }
                }

                if (!element.attributes.style || element.attributes.style.replace(/ /, "") == "" || element.attributes.style.replace(/ /, "") == ";")
                    delete element.attributes.style;
                else {
                    // FF problems with &quot; in url()
                    var style = element.attributes.style;
                    if (style.indexOf("url(&quot;") != -1) {
                        style = style.replace("url(&quot;", "url('");
                        style = style.replace("&quot;)", "')");
                        element.attributes.style = style;
                    }
                }

                return element;
            }
        },

        attributes: {
            style: function (value, element) {
                // Return #RGB for background and border colors
                return convertRGBToHex(value);
            }
        }
    });

}
// HTML 4 style /

/**
 * Convert a CSS rgb(R, G, B) color back to #RRGGBB format.
 * @param Css style string (can include more than one color
 * @return Converted css style.
 */
function convertRGBToHex(cssStyle) {
    if (cssStyle.indexOf("rgba") < 0) // chrome rgba
        return cssStyle.replace(/(?:rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\))/gi, function (match, red, green, blue) {
            red = parseInt(red, 10).toString(16);
            green = parseInt(green, 10).toString(16);
            blue = parseInt(blue, 10).toString(16);
            var color = [red, green, blue];

            // Add padding zeros if the hex value is less than 0x10.
            for (var i = 0; i < color.length; i++)
                color[i] = String('0' + color[i]).slice(-2);

            return '#' + color.join('');
        });


    return cssStyle.replace(/(?:rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\))/gi, function (match, red, green, blue, alpha) {
        red = parseInt(red, 10).toString(16);
        green = parseInt(green, 10).toString(16);
        blue = parseInt(blue, 10).toString(16);
        var color = [red, green, blue];

        // Add padding zeros if the hex value is less than 0x10.
        for (var i = 0; i < color.length; i++)
            color[i] = String('0' + color[i]).slice(-2);

        if (alpha == 0 && red == 0 && green == 0 && blue == 0)
            return 'transparent';
        else
            return '#' + color.join('');
    });

}