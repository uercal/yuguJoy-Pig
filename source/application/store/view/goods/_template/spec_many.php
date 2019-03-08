<!-- 商品规格属性模板 -->
<script id="tpl_spec_attr" type="text/template">
    {{ each spec_attr }}
    <div class="spec-group-item" data-index="{{ $index }}" data-group-id="{{ $value.group_id }}">
        <div class="spec-group-name">
            <span>{{ $value.group_name }}</span>
            <i class="spec-group-delete iconfont icon-shanchu1" title="点击删除"></i>
        </div>
        <div class="spec-list am-cf">
            {{ each $value.spec_items item key }}
            <div class="spec-item am-fl" data-item-index="{{ key }}">
                <span>{{ item.spec_value }}</span>
                <i class="spec-item-delete iconfont icon-shanchu1" title="点击删除"></i>
            </div>
            {{ /each }}
            <div class="spec-item-add am-cf am-fl">
                <input type="text" class="ipt-specItem am-fl am-field-valid">
                <button type="button" class="btn-addSpecItem am-btn am-fl">添加</button>
            </div>
        </div>
    </div>
    {{ /each }}
</script>

<!-- 商品规格table模板 -->
<script id="tpl_spec_table" type="text/template">
    <tbody>
    <tr>
        {{ each spec_attr }}
        <th>{{ $value.group_name }}</th>
        {{ /each }}
        <th>押金</th>
        <th>保修金</th>    
        <th>租赁价格</th>    
        <!-- <th>库存</th>         -->
    </tr>
    {{ each spec_list item }}
    <tr data-index="{{ $index }}" data-sku-id="{{ item.spec_sku_id }}">
        {{ each item.rows td itemKey }}
        <td class="td-spec-value am-text-middle" rowspan="{{ td.rowspan }}">
            {{ td.spec_value }}
        </td>
        {{ /each }}        
        <td>
            <input type="number" name="goods_price" min="0.1" value="{{ item.form.goods_price }}" class="am-field-valid ipt-w80"
                   required>
        </td>
        <td>
            <input type="number" name="secure_price" min="0.1" value="{{ item.form.secure_price }}" class="am-field-valid ipt-w80"
                   required>
        </td>        
        <td>
            <label for="">日租</label>
            <input type="number" name="rent_mode][day][price]" min="0.1" value="{{ item.form.rent_mode.day.price }}" class="am-field-valid ipt-w80"
                   required>
            <label for="">1~2月</label>
            <input type="number" name="rent_mode][month][ot]" min="0.1" value="{{ item.form.rent_mode.month.ot }}" class="am-field-valid ipt-w80"
                   required>                   
            <label for="">3~5月</label>
            <input type="number" name="rent_mode][month][tf]" min="0.1" value="{{ item.form.rent_mode.month.tf }}" class="am-field-valid ipt-w80"
                   required>
            <label for="">6月+</label>
            <input type="number" name="rent_mode][month][s]" min="0.1" value="{{ item.form.rent_mode.month.s }}" class="am-field-valid ipt-w80"
                   required>                   
            <label for="">1年</label>
            <input type="number" name="rent_mode][year][o]" min="0.1" value="{{ item.form.rent_mode.year.o }}" class="am-field-valid ipt-w80"
                   required>
            <label for="">2年</label>
            <input type="number" name="rent_mode][year][t]" min="0.1" value="{{ item.form.rent_mode.year.t }}" class="am-field-valid ipt-w80"
                   required>
        </td>        
        <!-- <td>
            <input type="number" name="stock_num" min="0.1" value="{{ item.form.stock_num }}" class="am-field-valid ipt-w80"
                   required>
        </td>        -->
    </tr>
    {{ /each }}
    </tbody>
</script> 