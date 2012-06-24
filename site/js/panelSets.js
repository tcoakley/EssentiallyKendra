var zumbaPanel = new Class({
    Extends: panel,
    options: {
        debug: false,
        name: 'zumba',
        panelSizes: {
            standard: {
                leftMargin: 10,
                width: 320,
                contentUrl: '/getContent.php?sn=zumba&fn=zumbaContent'
            },
            max: {
                leftMargin: 10,
                width: 900,
                contentUrl: '/getContent.php?sn=zumba&fn=zumbaContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 10
            },
            right: {
                leftMargin: null
            }
        }
    },
    initialize: function(options) {
        this.parent(options);

    },
    transition: function(targetSize, options) {
        this.parent(targetSize, options);
    }
});

var yogaPanel = new Class({
    Extends: panel,
    options: {
        debug: true,
        name: 'yoga',
        panelSizes: {
            standard: {
                leftMargin: 340,
                width: 320,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContent'
            },
            max: {
                leftMargin: 50,
                width: 900,
                contentUrl: '/getContent.php?sn=yoga&fn=yogaContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 50
            },
            right: {
                leftMargin: 920
            }
        }
    },
    initialize: function(options) {
        this.parent(options);
    }
});

var doTerraPanel = new Class({
    Extends: panel,
    options: {
        debug: true,
        name: 'doTerra',
        panelSizes: {
            standard: {
                leftMargin: 670,
                width: 320,
                contentUrl: '/getContent.php?sn=doTerra&fn=doTerraContent'
            },
            max: {
                leftMargin: 90,
                width: 900,
                contentUrl: '/getContent.php?sn=doTerra&fn=doTerraContentMax'
            }
        },
        slider: {
            left: {
                leftMargin: 90
            },
            right: {
                leftMargin: 960
            }
        }
    },
    initialize: function(options) {
        this.parent(options);
    }
});
