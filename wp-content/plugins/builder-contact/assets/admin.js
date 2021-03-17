(function ($) {
    'use strict';
    const contactFormBuilder = function (selector, data) {
        this.$table = $(selector);
        this.top = window.top.document;
        this.init(data);
    };

    contactFormBuilder.prototype = {
        $table: null,
        app: null,
        init: function (self) {
            this.app = self;
            this.loadExtraFields(self.values);
            this.makeSortable();
            this.events();

        },
        loadOrders: function (data, extra) {

            let orders;
            try {
                orders = JSON.parse(data['field_order']);
            } catch (e) {
            }

            if (!orders) {
                orders = {};
            }
            const tbody = this.$table[0].getElementsByTagName('tbody')[0],
                    items = tbody.getElementsByTagName('tr'),
                    sorted = [],
                    fr = document.createDocumentFragment();
            let add;
            for (let i = 0, len = items.length; i < len; ++i) {
                if (!items[i].classList.contains('tb_no_sort')) {
                    sorted.push(items[i]);
                }
                else {
                    add = items[i];
                }
            }
            sorted.sort(function (a, b) {
                let name1, name2, order1, order2,
                    is_extra1=a.classList.contains('tb_contact_new_row'),
                    is_extra2=b.classList.contains('tb_contact_new_row'),
                    getItem = function (v) {
                        for (let i = extra.length - 1; i > -1; --i) {
                            if ((extra[i].label === v || extra[i].id === v)  && extra[i].order !== undefined) {
                                return extra[i].order;
                            }
                        }
                        return false;
                    };
                name1 = is_extra1?a.getElementsByClassName('tb_new_field_textbox')[0].value:a.getElementsByClassName('tb_lb_option')[0].id;
                name1 = is_extra1 && '' === name1 ? a.getElementsByClassName('tb_new_field_textbox')[0].dataset.id : name1;
                name2 = is_extra2?b.getElementsByClassName('tb_new_field_textbox')[0].value:b.getElementsByClassName('tb_lb_option')[0].id;
                name2 = is_extra2 && '' === name2 ? a.getElementsByClassName('tb_new_field_textbox')[0].dataset.id : name2;
                name1 = name1.trim();
                name2 = name2.trim();
                order1 = orders[name1] !== undefined ? orders[name1] : (is_extra1 ? getItem(name1) : false);
                order2 = orders[name2] !== undefined ? orders[name2] : (is_extra2 ? getItem(name2) : false);

                return order1 - order2;
            });

            for (let i = 0, len = sorted.length; i < len; ++i) {
                fr.appendChild(sorted[i]);
            }
            fr.appendChild(add);
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            tbody.appendChild(fr);
        },
        loadExtraFields: function (data) {
            let options,
                    row = this.$table[0].getElementsByClassName('tb_no_sort')[0];
            try {
                options = JSON.parse(data['field_extra']).fields;
            } catch (e) {
            }
            if (!options) {
                options = {fields: []};
            }
            const fr = document.createDocumentFragment();
            for (let i = 0, len = options.length; i < len; ++i) {
                fr.appendChild(this.addField(options[i]));
            }
            row.parentNode.insertBefore(fr, row);
            this.loadOrders(data, options);
        },
        events: function () {
            const _this = this;
            this.$table
                    .on('click.tb_contact', '.tb_new_field_action', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const p = this.closest('.tb_no_sort');
                        p.parentNode.insertBefore(_this.addField({}), p);

                        _this.$table.find('tbody').sortable('refresh');
                        _this.changeObject();

                    })
                    .on('change.tb_contact', '.tb_new_field_type', function () {
                        _this.switchField(this);
                        _this.changeObject();
                    })
                    .on('click.tb_contact', '.tb_add_field_option', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.previousElementSibling.appendChild(_this.render.getOptions(['']));
                    })
                    .on('keyup.tb_contact', '.tb_contact_new_row input[type="text"], .tb_contact_new_row textarea', function () {
                        _this.changeObject();
                    })
                    .on('change.tb_contact', '.tb_contact_new_row .tb_new_field_required', function () {
                        _this.changeObject();
                    })
                    .on('click.tb_contact', '.tb_contact_value_remove,.tb_contact_field_remove', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (this.classList.contains('tb_contact_value_remove')) {
                            $(this).closest('li').remove();
                        }
                        else {
                            $(this).closest('.tb_contact_new_row').remove();
                        }
                        _this.changeObject();
                    });
        },
        makeSortable: function () {
            const _this = this;
            this.$table.find('tbody').sortable({
                items: 'tr:not(.tb_no_sort)',
                placeholder: 'ui-state-highlight',
                axis: 'y',
                containment: 'parent',
                update: function () {
                    _this.changeObject();
                }
            });
        },
        render: {
            call: function (data, type) {
                return this[type] === undefined ? this._default(data, type) : this[type].call(this, data, type);
            },
            setType: function (el, type) {
                el.setAttribute('data-type', type);
            },
            getText: function (data, type, inputType) {
                const input = document.createElement(inputType);
                    if(inputType !== 'textarea' ){
                        input.type = inputType !== 'tel'?'text': 'tel';
                    }
                if (data.value) {
                    input.value = data.value;
                }
                input.className = 'tb_new_field_value tb_field_type_text';
                this.setType(input, type);
                return input;
            },
            static: function (data, type) {
                const el = this._default(data, 'textarea');
                el.placeholder = tb_contact_l10n['static_text'];
                this.setType(el, 'static');
                return el;
            },
            upload:function(data,type){
                return document.createElement('div');
            },
            getOptions: function (opt) {
                const fr = document.createDocumentFragment();
                for (let i in opt) {
                    let li = document.createElement('li'),
                            a = document.createElement('a'),
                            input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'tb_multi_option';
                    input.value = opt[i];
                    a.className = 'tb_contact_value_remove tf_close';
                    a.href = '#';
                    li.appendChild(input);
                    li.appendChild(a);
                    fr.appendChild(li);
                }
                return fr;
            },
            _default: function (data, type) {
                if (type === 'text' || type === 'textarea' || type === 'tel') {
                    const inputType = type === 'textarea' ? type : 'input';
                    return this.getText(data, type, inputType);
                }
                const ul = document.createElement('ul'),
                        add = document.createElement('a'),
                        d = document.createDocumentFragment(),
                        opt = data.value || [''];
                ul.appendChild(this.getOptions(opt));
                d.appendChild(ul);
                add.href = '#';
                add.className = 'tb_add_field_option';
                add.textContent = tb_contact_l10n['add_option'];
                this.setType(add, type);
                d.appendChild(add);
                return d;
            }
        },
        addField: function (data) {
            const newItem = Object.keys(data).length === 0,
                selected = data.type ? data.type : 'text',
                    tr = document.createElement('tr'),
                    td = document.createElement('td'),
                    name = document.createElement('input'),
                    //type
                    colspan = document.createElement('td'),
                    selectWrap = document.createElement('div'),
                    fieldType = document.createElement('select'),
                    f = document.createDocumentFragment(),
                    control = document.createElement('div'),
                    newField = document.createElement('div'),
                    reqLabel = document.createElement('label'),
                    reqInput = document.createElement('input'),
                    remove = document.createElement('a'),
                    types = tb_contact_l10n.types,
                    uniq = 'tb_' + tb_app.Utils.generateUniqueID();


            control.className = 'control-input';
            newField.className = 'tb_new_field';
            selectWrap.className = 'selectwrapper';
            fieldType.className = 'tb_new_field_type tb_lb_option';
            tr.className = 'tb_contact_new_row';
            name.type = 'text';
            name.className = 'tb_new_field_textbox';
            name.value = data['label'] === undefined ? (true === newItem ? tb_contact_l10n['field_name'] : '') : data['label'];
            name.dataset.id = data['id'] === undefined ? '' : data['id'];
            reqInput.type = 'checkbox';
            reqInput.className = 'tb_new_field_required';
            reqInput.value = 'required';
            if (selected === 'static') {
                reqLabel.style['display'] = 'none';
            }
            if (data['required'] === true) {
                reqInput.checked = true;
            }
            remove.className = 'tb_contact_field_remove tf_close';
            remove.href = '#';

            colspan.setAttribute('colspan', '3');
            td.appendChild(name);
            tr.appendChild(td);
            for (let i in types) {
                let option = document.createElement('option');
                option.name = uniq;
                if (i === selected) {
                    option.selected = 'selected';
                }
                option.value = i;
                option.textContent = types[i];
                f.appendChild(option);
            }
            fieldType.appendChild(f);
            selectWrap.appendChild(fieldType);
            colspan.appendChild(selectWrap);
            control.appendChild(this.render.call(data, selected));
            newField.appendChild(control);
            reqLabel.appendChild(reqInput);
            reqLabel.appendChild(document.createTextNode(tb_contact_l10n['req']));
            newField.appendChild(reqLabel);
            colspan.appendChild(newField);
            colspan.appendChild(remove);
            tr.appendChild(colspan);
            return tr;
        },
        switchField: function (el) {
            const type = el.value,
                    control = el.closest('td').getElementsByClassName('control-input')[0],
                    req = control.closest('.tb_new_field').getElementsByClassName('tb_new_field_required')[0].parentNode;
            while (control.firstChild) {
                control.removeChild(control.firstChild);
            }
            control.appendChild(this.render.call({}, type));
            req.style['display'] = type === 'static' ? 'none' : '';
        },
        changeObject: function () {
            const items = this.$table[0].getElementsByTagName('tbody')[0].getElementsByTagName('tr'),
                    object = {fields: []},
            order = {};
            for (let i = 0, len = items.length; i < len; ++i) {//exclude new field button
                if (items[i].classList.contains('tb_contact_new_row')) {
                    let type = items[i].getElementsByClassName('tb_new_field_type')[0].options[items[i].getElementsByClassName('tb_new_field_type')[0].selectedIndex].value,
                            label = items[i].getElementsByClassName('tb_new_field_textbox')[0].value.trim(),
                            req = type !== 'static' && items[i].getElementsByClassName('tb_new_field_required')[0].checked === true,
                            value;
                    switch (type) {
                        case 'text':
                        case 'textarea':
                        case 'static':
                        case 'tel':
                            value = items[i].getElementsByClassName('tb_new_field_value')[0].value.trim();
                            break;
                        case 'radio':
                        case 'select':
                        case 'checkbox':
                            value = [];
                            let multi = items[i].getElementsByClassName('control-input')[0].getElementsByTagName('input');
                            for (let j = 0, len2 = multi.length; j < len2; ++j) {
                                let v = multi[j].value.trim();
                                if (v !== '') {
                                    value.push(v);
                                }
                            }
                            break;
                    }
                    if ((value !== '' && value !== undefined) || label !== '') {
                        let field = {
                            type: type,
                            order: i
                        };
                        if (req) {
                            field['required'] = req;
                        }
                        if (label !== '') {
                            field['label'] = label;
                        }else{
                            // Plan B for sorting solution
                            field['id'] = 'ex'+i;
                        }
                        if (value !== undefined && value !== '' && value.length > 0) {
                            field['value'] = value;
                        }
                        object.fields.push(field);
                    }
                }
                else if (!items[i].classList.contains('tb_no_sort')) {
                    let id = items[i].getElementsByClassName('tb_lb_option')[0].id;
                    order[id] = i;
                }
            }
            const el = this.top.getElementById('field_extra');
            el.value = JSON.stringify(object);
            const orderVal = JSON.stringify(order);
            this.top.getElementById('field_order').value = orderVal;
            this.app.settings['field_order'] = orderVal;
            Themify.triggerEvent(el, 'change');

        }
    };
    let isLoaded = null;
    tb_app.Constructor['contact_fields'] = {
        render: function (data, self) {
            const top = window.top;
            if (isLoaded === null) {
                isLoaded = true;
                top.Themify.LoadCss(tb_contact_l10n.admin_css, tb_contact_l10n.v);
            }
            let tr=document.createElement('tr');
            const table = document.createElement('table'),
                    thead = document.createElement('thead'),
                    tbody = document.createElement('tbody'),
                    tfoot = document.createElement('tfoot'),
                    f = document.createDocumentFragment(),
                    head = data.options.head,
                    body = data.options.body,
                    foot = data.options.foot,
                    render = {
                        text: function (id, placeholder, desc) {
                            const args = {
                                'id': 'field_' + id,
                                'placeholder': placeholder,
                                'class': 'large',
                                'type': 'text'
                            };
                            if (desc) {
                                args['help'] = desc;
                            }
                            return self.create([args]);
                        },
                        checkbox: function (id) {
                            const args = {
                                'id': 'field_' + id,
                                'new_line': true,
                                'type': 'checkbox',
                                options: [{value: '', name: 'yes'}]
                            };
                            if('sendcopy_active'===id){
                                args.binding = {
                                    checked:{show:['field_sendcopy_subject']},
                                    not_checked:{hide:['field_sendcopy_subject']}
                                };
                            }
                            if ( 'optin_active' === id ) {
                                args.binding = {
                                    checked : { show:['optin'] },
                                    not_checked : { hide:['optin'] }
                                };
                            }
                            return self.create([args]);
                        }
                    };
            //head
            for (let i in head) {
                let th = document.createElement('th');
                if(i==='l'){
                    th.colSpan=2;
                }
                th.textContent = head[i];
                tr.appendChild(th);
            }
            thead.appendChild(tr);
            //body
            for (let i in body) {
                tr = document.createElement('tr');
                for (let k in head) {
                    let td = document.createElement('td'),
                            el = null;
                    if (k === 'f') {
                        el = document.createElement('span');
                        td.textContent = body[i];
                    }else if (k === 'l') {
                        td.colSpan='2';
                        let d;
                        d = render.text(i + '_label',body[i]);
                        d.appendChild(render.text(i + '_placeholder',tb_contact_l10n['pl']));
                        if (i !== 'message') {
                            var tmp = document.createDocumentFragment(),
                                    checkbox = render.checkbox(i + '_require');
                            tmp.appendChild(d);
                            checkbox.querySelector('.tb_lb_option').appendChild(document.createTextNode(tb_contact_l10n['req']));
                            tmp.appendChild(checkbox);
                            el = tmp;
                        }else{
                            el = d;
                        }
                    }
                    else if (k === 'sh') {
                        el = render.checkbox(i + '_active');
                    }
                    if (el !== null) {
                        td.appendChild(el);
                    }
                    tr.appendChild(td);
                }
                f.appendChild(tr);
            }

            tr = document.createElement('tr');
            const td = document.createElement('td'),
                    a = document.createElement('a'),
                    plus = document.createElement('span');
            a.className = 'tb_new_field_action';
            a.href = '#';
            plus.className = 'tf_plus_icon';
            a.appendChild(plus);
            a.appendChild(document.createTextNode(data['new_row']));
            td.setAttribute('colspan', '4');
            td.appendChild(a);
            tr.className = 'tb_no_sort';
            tr.appendChild(td);
            f.appendChild(tr);
            tbody.appendChild(f);
            //footer
            for (let i in foot) {
                if (i !== 'align') {
                    tr = document.createElement('tr');
                    for (let k in head) {
                        if(k==='sh' && i==='send'){
                            continue;
                        }
                        let td = document.createElement('td'),
                                el = null;
                        if (k === 'f') {
                            td.textContent = foot[i];
                        }else if (k === 'l') {
                            td.colSpan=2;
                            let text = render.text(i + '_label', foot[i]);
                            if (i === 'send') {
                                td.colSpan=3;
                                var tmp = document.createDocumentFragment(),
                                        select = self.select.render({
                                            id: foot['align'].id,
                                            options: foot['align'].options
                                        }, self);
                                tmp.appendChild(text);
                                tmp.appendChild(select);
                                tmp.appendChild(document.createTextNode(foot['align'].label));
                                el = tmp;
                            }else if ( i === 'optin' ) {
								el = document.createDocumentFragment();
								let optin_provider = ThemifyConstructor.create( [
									{
										type : 'optin_provider',
										id : 'optin'
									}
								] );
								el.appendChild( text );
								el.appendChild( optin_provider );
							}
                            else {
                                el = text;
                            }


                            tmp.appendChild(checkbox);
                        }else if (k === 'sh' && i !== 'send') {
                            el = render.checkbox(i + '_active');
                            if(i==='captcha' && tb_contact_l10n['captcha']!==''){
                                el.querySelector('input').addEventListener('change',function(e){
                                    let p = this.closest('td').previousElementSibling;
                                    if(this.checked===true){
                                        const message = document.createElement('div');
                                        message.className='tb_captcha_message tb_field_error_msg';
                                        message.innerHTML = tb_contact_l10n['captcha'];
                                        p.appendChild(message);
                                    }
                                    else{
                                        let ch = p.getElementsByClassName('tb_captcha_message')[0];
                                        if(typeof ch !== "undefined"){
                                            ch.parentNode.removeChild(ch);
                                        }
                                    }
                                },{passive:true});
                            }
                        }
                        if (el !== null) {
                            td.appendChild(el);
                        }
                        if(k === 'l' && i === 'sendcopy'){
                            td.appendChild(self.create([{
                                'id': 'field_' + i + '_subject',
                                'after': tb_contact_l10n['sendcopy_sub'],
                                'type': 'text',
                                'class': 'small'
                            }]));
                        }
                        tr.appendChild(td);

                    }
                    f.appendChild(tr);
                }
            }
            tfoot.appendChild(f);

            table.className = 'contact_fields';
            table.appendChild(thead);
            table.appendChild(tbody);
            table.appendChild(tfoot);
            document.addEventListener('tb_editing_contact', function (e) {
                const instance = new contactFormBuilder(table, self);
                Themify.body.one('themify_builder_lightbox_close', function (e) {
                    instance.$table.off('click.tb_contact keyup.tb_contact change.tb_contact');
                });
            }, {once: true});

            return table;
        }
    };
})(jQuery);
