<div class="wrap" id="vue-app">
  <h2>Choose Ad Provider by country</h2>
  <div><small>Choose lower values for Intent, and higher for Smarter</small></div>
  <div class="country-control">
    <select2 :options="countries" class="dropdown-search" @input="addWidgetCountry">
      <option></option>
    </select>
  </div>

  <button @click.prevent="saveWidgetCountries" type="button" class="button button-primary" :class="{disabled: !dirty}" :disabled="!dirty">Save</button>

  <table class="wp-list-table widefat striped">
    <thead>
      <tr>
        <th>Code</th>
        <th>Country</th>
        <th>Search</th>
        <th>Rail</th>
        <th>Bottom</th>
        <th>Overlay</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(row, code) in widgetCountries">
        <td>{{ code }}</td>
        <td>{{ row.name }}</td>
        <td>
          <input type="number" v-model="row.w1" min="0" max="1" step="0.01" @input="rowUpdated(row)">
        </td>
        <td>
          <input type="number" v-model="row.w2" min="0" max="1" step="0.01" @input="rowUpdated(row)">
        </td>
        <td>
          <input type="number" v-model="row.w3" min="0" max="1" step="0.01" @input="rowUpdated(row)">
        </td>
        <td>
          <input type="number" v-model="row.w4" min="0" max="1" step="0.01" @input="rowUpdated(row)">
        </td>
        <?php /*
        <td>
          <label class="switch">
            <input type="checkbox" v-model="row.w1" @change="rowUpdated(row)" />
            <span class="slider round"></span>
            <span>&nbsp;</span>
          </label>
        </td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="row.w2" @change="rowUpdated(row)" />
            <span class="slider round"></span>
            <span>&nbsp;</span>
          </label>
        </td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="row.w3" @change="rowUpdated(row)" />
            <span class="slider round"></span>
            <span>&nbsp;</span>
          </label>
        </td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="row.w4" @change="rowUpdated(row)" />
            <span class="slider round"></span>
            <span>&nbsp;</span>
          </label>
        </td>
        */ ?>
        <td><a href="javascript:void(0)" @click.prevent="removeWidgetCountry(code)">Delete</a></td>
      </tr>
    </tbody>
  </table>

  <button @click.prevent="saveWidgetCountries" type="button" class="button button-primary" :class="{disabled: !dirty}" :disabled="!dirty">Save</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<style>
.country-control {
  width: 80%;
  margin: 30px auto;
  text-align: center;
}

.country-control .dropdown-search {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}

/* Checkbox switch */
.switch {
  position: relative;
  display: inline-block;
  margin: 0;
  width: 36px;
  height: 18px;
}

.switch input {display:none;}

.switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.switch .slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 2px;
  bottom: 2px;
  right: auto;
  background-color: white;
  transition: .4s;
}

/* Rounded sliders */
.switch .slider.round {
  border-radius: 18px;
}

.switch .slider.round:before {
  border-radius: 50%;
}

.switch input:checked + .slider {
  background-color: #2196F3;
}

.switch input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

.switch input:checked + .slider:before {
  left: auto;
  right: 2px;
}
/* END checkbox switch */
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>

var countries = <?= file_get_contents(dirname(__FILE__) . '/countries.js') ?>;
var select2data = countries.map(function(obj){
  return {
    id: obj.code,
    text: obj.name
  }
})
//select2data.unshift({id: '', text: ''})

jQuery(document).ready(function($){
  $('.1dropdown-search').select2({
    placeholder: 'Choose a country',
    data: select2data,
    allowClear: true,
    maximumSelectionLength: 10
  });
});

Vue.component('select2', {
  props: ['options', 'value'],
  template: '<select><slot></slot></select>',
  mounted: function () {
    var vm = this
    jQuery(this.$el)
      // init select2
      .select2({ data: this.options })
      .val(this.value)
      .trigger('change')
      // emit event on change.
      .on('change', function () {
        vm.$emit('input', this.value)
      })
  },
  watch: {
    value: function (value) {
      // update value
      $(this.$el)
        .val(value)
        .trigger('change')
    },
    options: function (options) {
      // update options
      $(this.$el).empty().select2({ data: options })
    }
  },
  destroyed: function () {
    $(this.$el).off().select2('destroy')
  }
})

var widgetCountries = JSON.parse('<?= json_encode(get_option('pp_widgets_widget_countries', [])) ?>') || {};

const app = new Vue({
  el: '#vue-app',
  data: {
    countries: select2data,
    _widgetCountries: Object.assign({}, widgetCountries),
    dirty: false,
    updating: false
  },
  computed: {
    widgetCountries: {
      get () {
        let w = this.$data._widgetCountries;
        for (let code in w) {
          let a = w[code];
          a.w1 = +a.w1;
          a.w2 = +a.w2;
          a.w3 = +a.w3;
          a.w4 = +a.w4;
        }

        return w;
      },
      set (value) {
        this.$data._widgetCountries;
      }
    }
  },
  methods: {
    saveWidgetCountries () {
      let url = '<?= admin_url("admin-ajax.php"); ?>';
      let data = new FormData
      data.append('action', 'pp_widgets_widget_countries')
      data.append('widgets', JSON.stringify(this.widgetCountries))

      this.updating = true
      axios.post(url, data)
      .then((response) => {
        // console.log('-->', response)
        this.dirty = false
        this.updating = false
      })
      .catch((error) => {
        console.error(error)
      })

    },
    rowUpdated (row, code) {
      this.dirty = true
    },
    removeWidgetCountry (code) {
      this.dirty = true
      this.$delete(this.widgetCountries, code)
    },
    addWidgetCountry (code) {
      let data = this.countries.filter(item => {
        return item.id == code 
      })

      if (!code || data.length !== 1) return

      code = code.toUpperCase()
      // if (code in this.widgetCountries) return
      this.dirty = true

      let country = data[0].text

      this.$set(this.widgetCountries, code, {
        name: country,
        w1: 0,
        w2: 0,
        w3: 0,
        w4: 0
      })
    }
  }
})
</script>