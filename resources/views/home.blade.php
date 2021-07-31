<!DOCTYPE html>
<html>
    <head>
    
    </head>
    <body>
        <form enctype="multipart/form-data">
            <input type="file" id="files" multiple>
        </form>
        <div id="pictures" ></div>
        <button id="show">Click</button>
        <button id="upload">Save</button>
        <script>
            let files = document.querySelector("#files");
            let pictures = document.querySelector("#pictures");
            let show = document.querySelector('#show');
            let upload = document.querySelector("#upload");

            var gallery = [];
            show.addEventListener('click',function(e){
                if(gallery.length > 0){
                    for(let obj of gallery){
                        var counter = 0;
                        let img = new Image();
                        img.src = obj.image;
                        img.height = 200;
                        img.width = 200;
                        let input = document.createElement('input');
                        let br = document.createElement('br');
                        input.value = obj.alt;
                        let container = document.createElement('div');
                        img.dataset.id = obj.index;
                        img.classList.add('images');
                        container.style.display = "inline-block";
                        container.appendChild(img);
                        container.appendChild(br);
                        container.appendChild(input);
                        pictures.appendChild(container);
                    }
                    console.log(gallery);
                }
            });
            document.addEventListener('click',function(e){
                console.log('hello');
                console.log(e.target);
                if(e.target.classList.contains('images')){
                    let index = e.target.dataset.id;
                    gallery = gallery.map(function(obj,ind){
                        if(obj.index != index){
                            return obj;
                        }
                    });
                    console.log(gallery);
                }
            });
            files.addEventListener('change',async function(e){
                let target = event.target;
                if(target.files.length > 0){
                   for(let file of target.files){
                       let reader = new FileReader();
                       reader.addEventListener('load',function(){
                          let index = 0;
                          if(gallery.length > 0){
                             index = gallery[gallery.length - 1].index
                          }
                          gallery.push({image:reader.result,alt:"",index:index})
                       });
                       reader.readAsDataURL(file);
                   }
                }
            });
            upload.addEventListener("click",async function(e){
                if(gallery.length > 0){
                    let formdata = new FormData();
                    for(let obj of gallery){
                        console.log(obj);
                        formdata.append('filer',obj)
                    }
                    let response = await fetch('http://127.0.0.1:8000/api/v1/create/filer',{
                        method:'POST', body:formdata,
                        headers:{
                            'Accept':'application/json',
                            'Content-Type':'application/form-data'
                        }
                    })
                    alert(JSON.stringify(await response.json()))
                }
            })
        
        </script>
    </body>

</html>