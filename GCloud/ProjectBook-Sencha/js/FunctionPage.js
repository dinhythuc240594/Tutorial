///////////////////////////////////////////////////////////////////////////////////////
/* Function */

/*START FUNCTION LoadData*/

function LoadData(button_create, dialog_update, dialog_delete, formsearch, key_word){

    Ext.Ajax.request({
        url: '/',
        method: 'GET',
        params: {
            'data':'json',
            'key_word': key_word
        },
        success: function(response){

            //convert text to JSON
            var jsonloadresponse = JSON.parse(response['responseText']);

            var records = [];

            for(var i = 0; i < jsonloadresponse.length; i++){

                //Array in Array
                records.push([
                    jsonloadresponse[i]['Id'],
                    jsonloadresponse[i]['Name'],
                    jsonloadresponse[i]['Type'],
                    jsonloadresponse[i]['Date']
                ]);

            }

            // define store
            var store = new Ext.data.Store({
                proxy: new Ext.ux.data.PagingMemoryProxy(records),
                remoteSort: true,
                reader: new Ext.data.ArrayReader({
                    fields: [
                        {name: 'id', type: 'int'},
                        {name: 'name', type: 'string'},
                        {name: 'type', type: 'string'},
                        {name: 'date', type: 'date'}
                    ]
                })
            });

            // create grid
            var grid = new Ext.grid.GridPanel({
                store: store,
                columns: [
                    {id: 'id', header: 'Id', width: 100, sortable: true, dataIndex: 'id'},
                    {header: 'Name', width: 350, sortable: true, dataIndex: 'name'},
                    {header: 'Type', width: 250, sortable: true, dataIndex: 'type'},
                    {header: 'Date', width: 250, sortable: true, dataIndex: 'date', renderer: Ext.util.Format.dateRenderer('d/m/Y')},
                    {xtype: 'actioncolumn',
                     header: 'Action',
                     width: 45,
                     items: [
                        {xtype: 'button',
                         icon: '../imgs/icon-update.png',
                         handler: function(grid, rowIndex, colIndex){
                            var update_book_id = store.getAt(rowIndex).get('id');

                            console.log(update_book_id);

                            Ext.Ajax.request({
                                url:'/update/',
                                method: 'GET',
                                params:{
                                    'update_book_id': update_book_id
                                },
                                success: function(response){
                                    console.log(response);

                                    jsonresponse = JSON.parse(response['responseText']);

                                    Ext.get('update_book_id').dom.value = jsonresponse[0]['Id'];
                                    Ext.get('update_book_name').dom.value = jsonresponse[0]['Name'];
                                    Ext.get('update_book_type').dom.value = jsonresponse[0]['Type'];
                                    Ext.get('update_book_date').dom.value = Ext.util.Format.date(jsonresponse[0]['Date'], 'd/m/Y');

                                    dialog_update.show();
                                }
                            });

                          }
                      }, {xtype: 'button',
                       icon: '../imgs/icon-delete.png',
                       handler: function(grid, rowIndex, colIndex){
                          var delete_book_id = store.getAt(rowIndex).get('id');

                          Ext.Ajax.request({
                              url:'/delete/',
                              method: 'GET',
                              params:{
                                  'delete_book_id': delete_book_id
                              },
                              success: function(response){
                                  jsonresponse = JSON.parse(response['responseText']);

                                  Ext.get('delete_book_id').dom.value = jsonresponse[0]['Id'];
                                  Ext.get('delete_book_name').dom.value = jsonresponse[0]['Name'];
                                  Ext.get('delete_book_type').dom.value = jsonresponse[0]['Type'];
                                  Ext.get('delete_book_date').dom.value = Ext.util.Format.date(jsonresponse[0]['Date'], 'd/m/Y');

                                  dialog_delete.show();
                              }
                          });

                        }
                    }]
                }],
                xtype: 'grid',
                layout: 'fit',
                stripeRow: true,
                frame: true,
                height: 330,
                title: 'Book',
                stateful: true,
                stateId: 'grid',
                tbar: [formsearch, '->', button_create],
                bbar: new Ext.PagingToolbar({
                    pageSize: 5,
                    store: store,
                    displayInfo: true,
                })
            });
            //render the gird to the specified div in the page
            grid.render('grid_data');
            store.load({params:{start:0, limit:5}});
        },
        failure: function(response){
            Ext.Msg.alert('Status', 'Request Failed.');
        }
    });
}

/*END FUNCTION LoadData*/

//////////////////////////////////////////////////////////////////////////////////////
/* START */

Ext.onReady(function(){
    Ext.QuickTips.init();

    //dialog create
    var dialog_create = new Ext.Window({
        title: 'Create Book',
        height: 170,
        width: 400,
        layout: 'fit',
        closeAction: 'hide',
        plain: true,
        items: new Ext.FormPanel({
                formId:'form_create',
                labelWidth: 75,
                frame:true,
                bodyStyle: 'padding:5px 5px 0',
                defaults: {width:230},
                defaultType: 'textfield',
                items:[
                    {fieldLabel: 'Name', name: 'name', allowBlank:false, id:'book_name'},
                    {fieldLabel: 'Type', name: 'type', allowBlank:false, id: 'book_type'},
                    {fieldLabel: 'Date',
                        xtype: 'datefield',
                        format: 'd/m/Y',
                        name: 'date',
                        allowBlank:false,
                        id: 'book_date',
                        onExpand: function(){
                            this.getPicker();
                        }
                    }
                ],
                buttons:[
                    {
                        text: 'Create',
                        handler: function(){

                            var name = Ext.get('book_name').dom.value;
                            var type = Ext.get('book_type').dom.value;
                            var date = Ext.get('book_date').dom.value;

                            if(name == '' || type == '' || date == ''){
                                return false;
                            }

                            Ext.Ajax.request({
                                url:'/create/',
                                method:'POST',
                                params:{
                                    'data': 'json',
                                    'book_name': name,
                                    'book_type': type,
                                    'book_date': date,
                                },
                                success: function(response){
                                    alert('Create successfully!');

                                    //refresh the same f5
                                    //window.location.reload();
                                    Ext.get('book_name').dom.value = '';
                                    Ext.get('book_type').dom.value = '';
                                    Ext.get('book_date').dom.value = '';

                                    Ext.fly('grid_data').update(' ');
                                    LoadData(button_create, dialog_update, dialog_delete, formsearch);
                                }
                            });
                            dialog_create.hide();
                        }
                    },
                    {
                        text: 'Close',
                        handler: function(){
                            dialog_create.hide();
                        }
                    }
                ]
            })
    });


    // button create
    var button_create = new Ext.Button({
        text: 'Create',
        width: 90,
        height: 40,
        scale: 'large',
        handler: function(){
            //Ext.get('button_create').on('click', function(){
                dialog_create.show();
            //});
        }
    });
    //button_create.render('button_create');

    //dialog update
    var dialog_update = new Ext.Window({
        title: 'Update Book',
        height: 200,
        width: 400,
        layout: 'fit',
        closeAction: 'hide',
        plain: true,
        items: new Ext.FormPanel({
                id:'form_update',
                frame:true,
                bodyStyle: 'padding:5px 5px 0',
                defaults: {width:230},
                defaultType: 'textfield',
                items:[
                    {fieldLabel: 'Id', name: 'id', allowBlank:false, id:'update_book_id', disabled: true},
                    {fieldLabel: 'Name', name: 'name', allowBlank:false, id:'update_book_name'},
                    {fieldLabel: 'Type', name: 'type', allowBlank:false, id: 'update_book_type'},
                    {fieldLabel: 'Date',
                        xtype: 'datefield',
                        format: 'd/m/Y',
                        name: 'date',
                        allowBlank:false,
                        id: 'update_book_date',
                        onExpand: function(){
                            this.getPicker();
                        }
                    }
                ],
                buttons:[
                    {
                        text: 'Update',
                        handler: function(){
                            var id = Ext.get('update_book_id').dom.value;
                            var name = Ext.get('update_book_name').dom.value;
                            var type = Ext.get('update_book_type').dom.value;
                            var date = Ext.get('update_book_date').dom.value;

                            if(name == '' || type == '' || date == ''){
                                return false;
                            }

                            Ext.Ajax.request({
                                url:'/update/',
                                method:'POST',
                                params:{
                                    'update_book_id': id,
                                    'update_book_name': name,
                                    'update_book_type': type,
                                    'update_book_date': date,
                                },
                                success: function(response){
                                    alert('Update successfully!');

                                    //refresh the same f5
                                    window.location.reload();
                                }
                            });
                            dialog_update.hide();
                        }
                    },
                    {
                        text: 'Close',
                        handler: function(){
                            dialog_update.hide();
                        }
                    }
                ]
            })
    });
    dialog_update.render('dialog_update');

    //dialog delete
    var dialog_delete = new Ext.Window({
        title: 'Delete Book',
        height: 200,
        width: 400,
        layout: 'fit',
        closeAction: 'hide',
        plain: true,
        items: new Ext.FormPanel({
                id:'form_delete',
                frame:true,
                bodyStyle: 'padding:5px 5px 0',
                defaults: {width:230},
                defaultType: 'textfield',
                items:[
                    {fieldLabel: 'Id', name: 'id', allowBlank:false, id:'delete_book_id', disabled: true},
                    {fieldLabel: 'Name', name: 'name', allowBlank:false, id:'delete_book_name', disabled: true},
                    {fieldLabel: 'Type', name: 'type', allowBlank:false, id: 'delete_book_type', disabled: true},
                    {fieldLabel: 'Date',
                        name: 'date',
                        id: 'delete_book_date',
                        allowBlank:false,
                        disabled: true
                    }
                ],
                buttons:[
                    {
                        text: 'Delete',
                        handler: function(){
                            var id = Ext.get('delete_book_id').dom.value;

                            Ext.Ajax.request({
                                url:'/delete/',
                                method:'POST',
                                params:{
                                    'delete_book_id': id,
                                },
                                success: function(response){
                                    alert('Delete successfully!');

                                    //refresh the same f5
                                    window.location.reload();
                                }
                            });
                            dialog_delete.hide();
                        }
                    },
                    {
                        text: 'Close',
                        handler: function(){
                            dialog_delete.hide();
                        }
                    }
                ]
            })
    });
    dialog_delete.render('dialog_delete');

    var formsearch = new Ext.FormPanel({
                    id:'form_search',
                    xtype: 'searchfield',
                    bodyStyle: 'padding:5px 5px 0',
                    defaults: {width:230},
                    defaultType: 'textfield',
                    items:[
                        {fieldLabel: 'Search',
                         name: 'search',
                         allowBlank:false,
                         id:'key_word',
                        }
                    ],
                    buttons:[
                        {text: 'Search',
                         handler: function(){
                            var key_word = Ext.get('key_word').dom.value;
                            if(key_word == ''){
                                return false;
                            }
                            Ext.fly('grid_data').update(' ');
                            LoadData(button_create, dialog_update, dialog_delete, formsearch, key_word);
                         }
                        }
                    ]
                })


    LoadData(button_create, dialog_update, dialog_delete, formsearch);
});

/* END */
