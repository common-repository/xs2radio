( function( blocks, components, editor, element, ServerSideRender ) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var useState = element.useState;
    var __ = wp.i18n.__;

    registerBlockType( 'xs2content/player', {
        title: __( 'XS2Content - Player', 'xs2radio' ),
        icon: 'playlist-audio',
        category: 'common',
        attributes: {
            postid: {
                type: 'number',
                default: 0,
            },
            showImage: {
                type: 'boolean',
                default: true,
            },
        },
        edit: function( props ) {
            const [ searchTerm, setSearchTerm ] = useState();

            var setPostID = function( postid ) {
                if (!postid) {
                    postid = 0;
                }

                props.setAttributes( { postid: postid } );
            };
            var setShowImage = function( showImage ) {
                props.setAttributes( { showImage: showImage } );
            };

            const options = wp.data.useSelect((select) => {
                const { getEntityRecords } = select('core');
                var query = {
                    search: searchTerm,
                    per_page: 3,
                    post__in: [props.attributes.postid]
                };
                const items = getEntityRecords('postType', 'post', query);
                if (!items) {
                    return [];
                }
                return items.map((post) => ({ label: post.title.rendered, value: post.id }));

                //return items;
            }, [searchTerm]);

            const handleKeydown = ( value ) => {
                setSearchTerm(value);
            };

            return el(
                'div',
                null,
                el( ServerSideRender, {
                    block: 'xs2content/player',
                    attributes: props.attributes,
                } ),
                el(
                    wp.blockEditor.InspectorControls,
                    {},
                    el(
                        wp.components.PanelBody,
                        {},
                        el(
                            wp.components.ComboboxControl,
                            {
                                label: "Post",
                                value: props.attributes.postid,
                                onFilterValueChange: handleKeydown,
                                onChange: setPostID,
                                options: options
                            }
                        ),
                        el(
                            wp.components.ToggleControl,
                            {
                                label: 'Show image',
                                checked: props.attributes.showImage,
                                onChange: setShowImage,
                            }
                        ),
                    ),
                ),
            );
        },
        save: function( props ) {
            var shortcode = '[xs2content-player postid=' + props.attributes.postid;

            if (props.attributes.showImage) {
                shortcode +=  ' image=' + props.attributes.showImage;
            }

            return shortcode + ']';
        }
    } );

    registerBlockType( 'xs2content/playlist', {
        title: __( 'XS2Content - Playlist', 'xs2radio' ),
        icon: 'playlist-audio',
        category: 'common',
        attributes: {
            amount: {
                type: 'number',
                default: 5,
            },
            category: {
                type: 'number',
                default: 0,
            },
            showImage: {
                type: 'boolean',
                default: true,
            },
        },
        edit: function( props ) {
            const [ searchTerm, setSearchTerm ] = useState();

            var setAmount = function( amount ) {
                props.setAttributes( { amount: amount } );
            };
            var setCategory = function( category ) {
                if (!category) {
                    category = 0;
                }

                props.setAttributes( { category: category } );
            };
            var setShowImage = function( showImage ) {
                props.setAttributes( { showImage: showImage } );
            };

            const options = wp.data.useSelect((select) => {
                const { getEntityRecords } = select('core');
                var query = {
                    search: searchTerm,
                    per_page: 30,
                };
                const items = getEntityRecords('taxonomy', 'category', query);
                if (!items) {
                    return [];
                }
                return items.map((term) => ({ label: term.name, value: term.id }));
            }, [searchTerm]);

            const handleKeydown = ( value ) => {
                setSearchTerm(value);
            };


            return el(
                'div',
                null,
                el( ServerSideRender, {
                    block: 'xs2content/playlist',
                    attributes: props.attributes,
                } ),
                el(
                    wp.blockEditor.InspectorControls,
                    {},
                    el(
                        wp.components.PanelBody,
                        {},
                        el(
                            wp.components.TextControl,
                            {
                                label: 'Amount',
                                value: props.attributes.amount,
                                onChange: setAmount
                            }
                        ),
                        el(
                            wp.components.ComboboxControl,
                            {
                                label: "Category",
                                value: props.attributes.category,
                                onFilterValueChange: handleKeydown,
                                onChange: setCategory,
                                options: options
                            }
                        ),
                        el(
                            wp.components.ToggleControl,
                            {
                                label: 'Show image',
                                checked: props.attributes.showImage,
                                onChange: setShowImage,
                            }
                        ),
                    ),
                ),
            );
        },
        save: function( props ) {
            var shortcode = '[xs2content-playlist amount=' + props.attributes.amount;

            if (props.attributes.category) {
                shortcode +=  ' category=' + props.attributes.category;
            }

            if (props.attributes.showImage) {
                shortcode +=  ' image=' + props.attributes.showImage;
            }

            return shortcode + ']';
        }
    } );
} )(
    window.wp.blocks,
    window.wp.components,
    window.wp.editor,
    window.wp.element,
    window.wp.serverSideRender,
);