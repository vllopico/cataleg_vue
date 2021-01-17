new Vue
({
	el: '#app',
	vuetify: new Vuetify(),
	data:
	{
		cataleg: [],
		catalegOrg: [],
		comanda:[],
		categories:[],
		page:1,
		long:8,
		dialogComanda: false,
		txtBuscar:'',
		alertaBuscar:false,
		btnComanda: false,
		c_totalbase: 0,
		c_totaliva: 0,
		c_total:0
	},
	methods:
	{
		omplir_cataleg()
		{
			let _this = this;
			//fetch('./data.php', {method:'GET'})
			fetch('./productes.json', {method:'GET'})
			.then(function(response) {
				return response.json();
			})
			.then(function(data) {
				data.forEach(function(item){
					_this.cataleg.push({"referencia":item.referencia, "nom":item.nom, "preu":item.preu, "qini":1, "categoria":item.categoria, "imatge":item.imatge, "iva":item.iva});
					if(_this.categories.indexOf(item.categoria) === -1) {
						_this.categories.push(item.categoria);
						}
					})
				//Calculem la pàginació. 8 per fulla.
				_this.long = Math.ceil(_this.cataleg.length / 8);
				_this.catalegOrg = _this.cataleg;
				
			})
			.catch(function(err) {
				console.error(err);
			});
		},
		afegiralcarret(index){
			
			var i = 0;
			var esta = false;
			var base = parseInt(this.cataleg[index].qini) * parseFloat(this.cataleg[index].preu);
			var totaliva = base * (1+parseFloat(this.cataleg[index].iva/100)) - base;
			var total = base + totaliva;
			
			while (i<this.comanda.length)
			{
				if (this.comanda[i].referencia == this.cataleg[index].referencia) {
					this.comanda[i].quantitat = this.cataleg[index].qini;
					this.comanda[i].base = base;
					this.comanda[i].totaliva = totaliva.toFixed(2);
					this.comanda[i].total = total.toFixed(2);
					esta = true;
					
					this.c_totalbase = base;
					this.c_totaliva = totaliva;
					this.c_total = total;
					
				}
				i = i + 1;
			}
			if (!esta) {
				this.comanda.push({"referencia":this.cataleg[index].referencia,
					"producte":this.cataleg[index].nom,
					"quantitat":this.cataleg[index].qini, 
					"preu":this.cataleg[index].preu, 
					"iva":this.cataleg[index].iva,
					"base": base,
					"totaliva": totaliva.toFixed(2),
					"total":total.toFixed(2)
				});
				this.c_totalbase += base;
				this.c_totaliva += totaliva;
				this.c_total += total;
			}
		},
		ferComanda()
		{
			this.dialogComanda = true;
		},
		imprimirComanda()
		{
			var _data = {"num":1};
			var fetchData = {method: "POST",headers: {"Content-type": "application/json; charset=UTF-8"}, body: JSON.stringify(this.comanda)};
			fetch('http://localhost/cataleg_vue/imprimirComanda.php', {method:"POST", body:JSON.stringify(this.comanda)})
			.then(response => response.json())
			.then(function(json){
				console.log(json);
				let a = document.createElement('a');
				a.href = "http://localhost/cataleg_vue/pdfs/"+json.file;
				a.target = "_blank";
				document.body.appendChild(a);
				a.click();    
				a.remove();   
			})
			.catch(err => console.log(err));
		},
		buscarProducte(){
			let cat = this.catalegOrg.filter(item => item.nom.includes(this.txtBuscar) || item.referencia.includes(this.txtBuscar));
			this.page = 1;
			this.long = Math.ceil(cat.length / 8);
			this.cataleg = cat;
			if (this.cataleg.length == 0) {this.alertaBuscar = true;} else {this.alertaBuscar = false;}
		},
		borrarLineaComanda(referencia)
		{
			var i = 0;
			var base = 0.0;
			var totaliva = 0.0;
			var total = 0.0;
			
			while (i<this.comanda.length)
			{
				if (this.comanda[i].referencia === referencia)
				{
					base = parseInt(this.comanda[i].quantitat) * parseFloat(this.comanda[i].preu);
					totaliva = base * (1+parseFloat(this.comanda[i].iva/100)) - base;
					total = base + totaliva;
					this.comanda.splice(i,1);
				}
				i = i + 1;
			}
			this.c_totalbase = this.c_totalbase - base;
			this.c_totaliva = this.c_totaliva - totaliva;
			this.c_total = this.c_totalbase + this.c_totaliva;
		},
		producteCategoria(categoria){
			let cat = this.catalegOrg.filter(item => item.categoria == categoria);
			this.long = Math.ceil(cat.length / 8);
			this.page = 1;
			this.cataleg = cat;
			this.alertaBuscar = false;
		},
		pathImage(imatge){ 
			return "img/"+imatge;
		}
	},
	computed: {
		pagProductes() {
			const number = 8;
			return this.cataleg.slice((this.page - 1) * number, this.page * number);
		}
	},
	created: function(){ 
		this.omplir_cataleg();
	}
})
