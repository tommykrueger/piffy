const componentTemplate = document.createElement('template');
componentTemplate.innerHTML = `
<style>
:root {
  --icon-size: 32px;
  --star-color: gold;
} 
star-rating {
  display: flex;
  justify-content: center;
  align-items: center;
}
.star-rating {
  margin: 0;
  padding: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: black;
  text-align: center;
  border-radius: 4px;
  border: 1px solid var(--border-color);
  position: relative;
  overflow: hidden;
  text-transform: uppercase;
  background: linear-gradient(180deg, #fff, var(--border-color));
  transition: all 150ms ease-in-out;
  box-shadow: rgba(0,0,0,.35) 0 3px 6px;
}
.icon-rating {
  width: var(--icon-size);
  height: var(--icon-size);
  display: block;
  margin-right: 10px;
  border-radius: 50%;
  font-style: normal;
  background-color: var(--border-color);
}
.star-rating span {
  width: 32px;
  height: 32px;
  display: block;
  cursor: pointer;
  order: 1;
}
.star-rating #d1 {
  margin-left: 40px;
}
.star-rating span {
  padding: 0 2px;
}
.star-rating span:hover path,
.star-rating path {
  fill: var(--star-color);
}
.star-rating span:hover ~ span path {
  fill: grey;
}
.rating i::before {
  content: '😄';
  font-size: 26px;
  line-height: 32px;
}
#d5:hover ~ i::before {
  content: '😄';
}
#d4:hover ~ i::before {
  content: '🙂';
}
#d3:hover ~ i::before {
  content: '😐';
}
#d2:hover ~ i::before {
  content: '☹️';
}
#d1:hover ~ i::before {
  content: '😥️';
}
.star-rating svg {
  width: 100%;
  height: 100%;
  margin: 0 3px;
}
  
  

.rating-body:not(:checked)>label {
  color: #ccc;
}

.rating-body {
  /*gap: 4px;*/
  display: flex;
  flex-direction: row-reverse;
  justify-content: start;
  align-items: center;
}

.rating-body label {
  cursor: pointer;
  transition: color 0.2s ease;
}

.rating-body label {
  font-size: var(--icon-size);
}

input[type="radio"] {
  display: none;
}

/* change the color of all label after the current check radio label. since we have reverse the flex direction
therefore the label before the checked label affected by this */
.rating-body > input:checked ~ label {
  color: var(--star-color);
}

/* when we selected any star and again hover on it so i will set all the star color
to default so that the below hover works fine on all the stars */
.rating-body:hover input:checked ~ label {
  color: #ccc;
}

/* change the color to orange for the label which we hovered */
.rating-body > label:hover,
/* change the color of all label after the current hovered label. since we have reverse the flex direction
therefore the label before the hovered label affected by this */
.rating-body > label:hover ~ label {
  color: var(--star-color) !important;
}

.rating-average {
  color: black;
  font-size: 18px;
  font-weight: bold;
  margin-right: 12px;
}  
.number-of-ratings {
  font-size: 12px;
  margin-left: 12px;
}  

</style>

<span class="rating-average"></span>
<div class="rating-body"></div>
<span class="number-of-ratings"></span>

<!--
<div class="star-rating">
    <span id="d1">
        <svg xmlns="http://www.w3.org/2000/svg" width="1235" height="1175" viewbox="0 0 1235 1175">
            <path fill="#de0000" d="M0,449h1235l-999,726 382-1175 382,1175z"/>
        </svg>
    </span>
    <span id="d2">
        <svg xmlns="http://www.w3.org/2000/svg" width="1235" height="1175" viewbox="0 0 1235 1175">
            <path fill="#de0000" d="M0,449h1235l-999,726 382-1175 382,1175z"/>
        </svg>
    </span>
    <span id="d3">
        <svg xmlns="http://www.w3.org/2000/svg" width="1235" height="1175" viewbox="0 0 1235 1175">
            <path fill="#de0000" d="M0,449h1235l-999,726 382-1175 382,1175z"/>
        </svg>
    </span>
    <span id="d4">
        <svg xmlns="http://www.w3.org/2000/svg" width="1235" height="1175" viewbox="0 0 1235 1175">
            <path fill="#de0000" d="M0,449h1235l-999,726 382-1175 382,1175z"/>
        </svg>
    </span>
    <span id="d5">
        <svg xmlns="http://www.w3.org/2000/svg" width="1235" height="1175" viewbox="0 0 1235 1175">
            <path fill="#de0000" d="M0,449h1235l-999,726 382-1175 382,1175z"/>
        </svg>
    </span>
    <i class="icon-rating"></i>
</div>
-->
<!-- <button class="rateButton">rate</button> -->

`;

const TAG_NAME = 'star-rating';


export default class StarRating extends HTMLElement {

  static observedAttributes = [
    'stars',
    'rating',
    'sum',
    'max',
    'value',
    'id'
  ];

  constructor() {
    super();

    this.attr = {
      stars: 0,
      rating: 0.0,
      sum: 0,
      max: 5,
      value: 0,
      id: 0,
    };

    this.uniqueId = this.getRandomNum();

    this._clonedNode = componentTemplate.content.cloneNode(true);
    // attribute reflected values
    // this.attachShadow({mode: 'open'});
  }

  get value() {
    let checkedEl = this.querySelector('input[type="radio"]:checked');
    return checkedEl ? checkedEl.value : 0;
  }

  init() {
    //alert(this.attr.value);
    //this.value = this.attr.value;
    //alert(parseInt(this.attr.max) - 1);

    for (let i = parseInt(this.attr.max); i > 0; i--) {
      this._clonedNode.querySelector('.rating-body').innerHTML += this.getStarTemplate(i);
    }

    let val = Math.round(this.attr.rating);
    if (val) {
      this._clonedNode.querySelector(`#star-${this.uniqueId}-${val}`).checked = true;
    }

    this._clonedNode.querySelector('.rating-average').innerHTML = `${this.attr.rating}`;
    this._clonedNode.querySelector('.number-of-ratings').innerHTML = `${this.attr.sum}`;

    // this._clonedNode.querySelector('.rating').innerHTML += `<i class="icon-rating"></i>`;

  }

  connectedCallback() {
    this.reset();
    this.init();
    this.appendChild(this._clonedNode);

    this.querySelectorAll('input[type="radio"]').forEach(
      input => input.addEventListener('change', (e) => {
        const value = this.value;
        if (!value) {
          console.log('no value');
          return;
        }

        this.save('/ajax/star-rating/', {
          id: this.attr.id,
          stars: value
        });

      }));

    /*
    this.querySelector('.rateButton').addEventListener('click', (e) => {
      e.preventDefault();

      const value = this.value;
      if (!value) {
        console.log('no value');
        return;
      }

      this.save('/ajax/star-rating/', {
        id: this.attr.id,
        stars: value
      });
    });

     */

  }

  reset() {
    this.innerHTML = '';
  }

  save(url = null, data = {}, type = 'POST') {
    if (!url) {
      return false;
    }

    this.request = new XMLHttpRequest();
    this.request.onreadystatechange = (s) => {
      if (s.readyState === 4 && s.status === 200) {
        console.log('bubresp', s);
      }
    };
    this.request.responseType = 'json';
    this.request.withCredentials = true;

    this.request.onload = () => {
      console.log(this.request.response);
    };

    this.request.onerror = (r) => {
      console.log(r);
    };

    this.request.open(type, url, true);

    if (type === 'POST') {
      this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      this.request.send('data=' + JSON.stringify(data));
    } else {
      this.request.send();
    }
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }
    this.attr[name] = newValue;
  }

  getStarTemplate(num) {
    return `
      <input id="star-${this.uniqueId}-${num}" type="radio" value="${num}" name="rating-${this.uniqueId}"/>
      <label for="star-${this.uniqueId}-${num}">★</label>
    `;
  }

  getRandomNum() {
    return Math.random().toString(36).slice(2, 7);
  }

}

customElements.define(TAG_NAME, StarRating);