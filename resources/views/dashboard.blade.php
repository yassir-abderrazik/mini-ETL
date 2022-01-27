@extends('layouts.app')
@section('jquery')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
    <script src="{{ asset('js/jquery.min.js') }}" defer></script>
@endsection
@section('content')
    <div id="successMessage"></div>

    <div class="w-full ">
        <div x-data="setup()">
            <ul class="flex justify-center items-center w-full my-4">
                <template x-for="(tab, index) in tabs" :key="index">
                    <li class="cursor-pointer py-2 px-4 text-gray-500 border-b-8"
                        :class="activeTab===index ? 'text-green-500 border-green-500' : ''" @click="activeTab = index"
                        x-text="tab"></li>
                </template>
            </ul>
            <div class="md:w-2/3 mx-auto  bg-white p-16 text-center border  ">
                <div x-show="activeTab===0">
                    <div class=" bg-gray-50 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                        <div class="w-full sm:max-w-md p-5 mx-auto">
                            <form action="">
                                <div class=" my-4">
                                    <label class="block text-left my-4" for="email">Select a file and specify the file
                                        properties :</label>
                                    <input type="file" name="file" id="file"
                                        class="file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-lg file:font-semibold
                                    file:bg-violet-50 file:text-green-500
                                    hover:file:bg-violet-100"
                                        accept=".xls,.xlsx,.csv">
                                </div>
                                <div class=" my-4">
                                    <label class="block text-left my-4" for="email">Delimiter :</label>
                                    <select name="delimiter" id="delimiter" class="w-full">
                                        <option value=";">;</option>
                                        <option value=":">:</option>
                                        <option value="&#9;">tab</option>
                                        <option value="&#32;">Space</option>
                                        <option value="|">|</option>
                                        <option value="&#44;">,</option>
                                    </select>
                                </div>
                                <div id="errorFile"></div>
                                <button type="button" class="p-4 bg-green-500 text-white rounded-xl"
                                    onclick="AddFile()">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div x-show="activeTab===1">
                    <div class=" my-4 hidden">
                        <label class="block text-left my-4" for="email">Data rows to skip :</label>
                        <input type="number" name="rowsToSkip" class="w-full" id="rowsToSkip">
                    </div>
                    <div id="table">

                    </div>
                </div>
                <div x-show="activeTab===2">
                    <div>
                        <table class="w-full">
                            <thead>
                                <tr class="w-full p-8 bg-green-500 text-white">
                                    <th class="p-3">Name</th>
                                    <th class="p-3">Data Type</th>
                                    <th class="p-3">New Type</th>

                                </tr>
                            </thead>
                            <tbody id="dataConversion" class="w-full">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div x-show="activeTab===3">
                    <form action="">
                        <label class="block text-left my-4" for="db">Select Database :</label>
                        <div class="flex items-center my-4">
                            <select name="db" id="db" class="w-full mx-2 rounded-lg">
                            </select>

                            <button type="button" class="bg-red-500 p-2 " onclick="getDB()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"
                                    class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                    <path
                                        d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z" />
                                    <path fill-rule="evenodd"
                                        d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z" />
                                </svg>
                            </button>
                        </div>
                        {{-- modal --}}
                        <div x-data="{ open: false }">
                            <div class="px-4 py-2 bg-gray-400 hover:bg-gray-700 text-white text-xl font-serif rounded-full border-none focus:outline-none cursor-pointer"
                                @click="open = true">Add new Database</div>
                            <div class="fixed z-1 w-full h-full top-0 left-0 flex items-center justify-center" x-cloak
                                x-show="open">
                                <div class="fixed w-full h-full bg-gray-500 opacity-50"></div>
                                <div class="relative z-2 w-3/12 bg-white p-8 mx-auto rounded-xl flex flex-col items-center"
                                    @click.away="open = false">
                                    <div class="my-4">
                                        <label class="block text-left my-4" for="email">Database Name:</label>
                                        <input type="text" name="addNewDatabase" class="w-full" id="addNewDatabase">
                                    </div>
                                    <div id="messageAddDb"></div>
                                    <div class="flex">
                                        <a class="px-4 py-2  m-2 bg-red-400 cursor-pointer hover:bg-red-700 text-white text-lg rounded-lg"
                                            @click="open = false">Close</a>
                                        <a onclick="addNewDatabase()"
                                            class="px-4 py-2 m-2 bg-green-400 cursor-pointer hover:bg-green-700 text-white text-lg rounded-lg">
                                            Ajouter</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label class="block text-left my-4" for="db">Create Table :</label>
                        <div class="flex items-center my-4">
                            <textarea name="createTableQuery" id="createTableQuery" class="w-full mx-2 rounded-lg" disabled>
                                                            </textarea>

                            
                        </div>
                        <button type="button" class="bg-red-500 p-2 text-white rounded-xl" onclick="createTableAndAddData()">
                            Add Table and Save Data
                        </button>
                    </form>

                </div>
            </div>
            <div class="flex gap-4 justify-center border-t p-4">
                <button
                    class="py-2 px-4 border rounded-md border-green-600 text-green-600 cursor-pointer uppercase text-sm font-bold hover:bg-green-500 hover:text-white hover:shadow"
                    @click="activeTab--" x-show="activeTab>0">Back</button>
                <button
                    class="py-2 px-4 border rounded-md border-green-600 text-green-600 cursor-pointer uppercase text-sm font-bold hover:bg-green-500 hover:text-white hover:shadow"
                    @click="activeTab++" x-show="activeTab<tabs.length-1">Next</button>
            </div>
        </div>
        <!--actual component end-->
    </div>



@endsection
@section('script')

    <script>
        let fileName;
        let data;
        let headerTable = [];
        let typeColumn = [];

        function setup() {
            return {
                activeTab: 0,
                tabs: [
                    "CSV file",
                    "Columns",
                    "Data conversion",
                    "Destination",
                ]
            };
        }

        function AddFile() {

            //get input file 
            const file = document.getElementById('file');
            fileName = file.files[0].name.split('.');
            // check file extension
            if (file.files[0].name.split('.').pop() == "csv") {
                // read file
                const input = file.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    // get file content
                    const str = e.target.result;
                    // converting csv to json  and saved in data
                    data = csvToArray(str);

                    // get hidden input rows to skip
                    const rowsToSkip = document.getElementById('rowsToSkip');
                    // remove hidden 
                    rowsToSkip.parentNode.classList.remove("hidden")
                    // set max in min ( rows number)
                    rowsToSkip.max = data.length;
                    rowsToSkip.min = '1';

                    // change Json To table and save it  at column Tab using JSONToTAble accept one parameter (data)
                    JSONToTAble(data)
                    // show success message 
                    successMessage();
                    // delete success message after 4s
                    setTimeout(() => {
                        deleteSuccessMessage()
                    }, 4000);


                };
                reader.readAsText(input);

            } else if (file.files[0].name.split('.').pop() == "xls" || file.files[0].name.split('.').pop() == "xlsx") {
                // read file
                let fileReader = new FileReader();
                // method is used to start reading the contents of the specified file
                fileReader.readAsBinaryString(file.files[0]);
                fileReader.onload = (e) => {
                    // get file content
                    let result = e.target.result;
                    // function  to parse data using xlsx package
                    let workbook = XLSX.read(result, {
                        type: "binary"
                    });
                    workbook.SheetNames.forEach(sheet => {
                        // create json 
                        // converting the sheet to JSON outputs all cells as a string using raw: false
                        data = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheet], {
                            raw: false
                        });
                        // change Json To table and save it  at column Tab using JSONToTAble accept one parameter (data)
                        JSONToTAble(data)
                    });
                    // show success message 
                    successMessage();
                    // delete success message after 4s
                    setTimeout(() => {
                        deleteSuccessMessage()
                    }, 4000);
                }
            } else {
                // the file type must be csv, xlsx or xls
                const errorFile = document.getElementById('errorFile');
                errorFile.innerHTML = ''
                errorFile.innerHTML +=
                    '<div class="text-white bg-red-500 m-1 p-2">the file type must be csv, xlsx or xls </div>'

            }
        }

        function csvToArray(str) {
            const delimiter = document.getElementById('delimiter').value;
            const headers = str.slice(0, str.indexOf("\r\n")).split(delimiter);
            headerTable = headers;
            // typeColumn = new Array(headers.length).fill('varchar(255)')
            const rows = str.slice(str.indexOf("\n") + 1).split("\r\n");
            const arr = rows.map(
                function(row) {
                    const values = row.split(delimiter).reduce((accum, curr) => {
                        if (accum.isConcatting) {
                            accum.soFar[accum.soFar.length - 1] += delimiter + curr

                        } else {
                            accum.soFar.push(curr)


                        }
                        if (curr.split('"').length % 2 == 0) {

                            accum.isConcatting = !accum.isConcatting
                        }
                        return accum;
                    }, {
                        soFar: [],
                        isConcatting: false
                    }).soFar;
                    for (var i = 0; i < values.length; i++) {
                        values[i] = values[i].replace(/"/g, '');
                    }
                    const el = headers.reduce(
                        function(object, header, index) {

                            object[header] = values[index];
                            return object;
                        }, {}
                    );
                    return el;
                }
            );
            return arr;
        }

        function JSONToTAble(data) {

            var col = [];

            // put key names in array
            for (var key in data[0]) {
                
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
            headerTable = col;
            typeColumn = new Array(col.length).fill('varchar(255)')
            console.log(typeColumn);
            var dataConversion = document.getElementById("dataConversion");
            dataConversion.innerHTML = '';

            // Create a table.
            var table = document.createElement("table");
            table.classList = " w-full leading-normal border-collapse";
            // Create table header row using the extracted headers above.


            // create thead inside table and add class list
            var thead = table.createTHead();
            thead.classList = "flex w-full";

            // create tr inside thead and add class list
            var trhead = thead.insertRow(0);
            trhead.classList = "flex w-full";

            // add head to table using col array
            for (var i = 0; i < col.length; i++) {

                dataConversion.innerHTML += `<tr><td class="p-3 border">` + col[i] + `
                    </td><td class="p-3 border">` + typeof(col[i]) + `</td><td class="p-3 border">
                        <select class="p-4 w-full rounded-xl" id="` + col[i] + `" onchange="DataConversion('` + col[
                    i] + `')" > 
                            <option selected>------choose type-------</option>    
                            <option value="string">String</option>    
                            <option value="date">Date</option>    
                            <option value="integer">Integer</option>    
                            <option value="double">Double</option>    
                            <option value="boolean">Boolean</option>    
                        </select></td>
                    </tr>`
                var th = document.createElement("th");
                th.classList = "p-4 w-1/4 bg-green-500 text-white";
                // table header.
                th.innerHTML = col[i] + ' ( ' + typeof(col[i]) + ' )';
                trhead.appendChild(th);
            }
            // add tbody element inside table + class
            var tbody = table.createTBody();
            tbody.classList = "flex flex-col items-center justify-between overflow-y-scroll w-full h-80";
            // add json data to the table as rows.
            for (var i = 0; i < data.length; i++) {
                // insert record in tr reverse
                var tr = tbody.insertRow(-1);
                tr.classList = "flex w-full ";
                // add td Cell for each tr
                for (var j = 0; j < col.length; j++) {
                    var tabCell = tr.insertCell(-1);
                    tabCell.classList = "p-4 w-1/4 border "
                    tabCell.innerHTML = data[i][col[j]];
                }
            }

            // Now, add the newly created table with json data, to a container.
            var showData = document.getElementById('table');
            showData.innerHTML = "";
            showData.appendChild(table);
        }

        function DataConversion(column) {
            let changeTo = document.getElementById(column).value;
            let message = "";
            let indexColumn = headerTable.indexOf(column); 
            if (changeTo == "integer") {
                data.forEach(function(element, index) {
                    let newTypeData = parseInt(element[column])
                    if (isNaN(newTypeData)) {
                        return;
                    }
                    typeColumn[indexColumn] = 'int';
                    element[column] = newTypeData;
                });
            } else if (changeTo == "double") {
                data.forEach(function(element, index) {
                    let newTypeData = parseFloat(element[column]);
                    if (isNaN(newTypeData) == NaN) return;
                    typeColumn[indexColumn] = 'double';
                    element[column] = newTypeData;
                });
            } else if (changeTo == "boolean") {
                data.forEach(function(element, index) {
                    let newTypeData = Boolean(element[column]);
                    if (isNaN(newTypeData) == NaN) return;
                    typeColumn[indexColumn] = 'boolean';
                    element[column] = newTypeData;
                });
            } else if (changeTo == "date") {
                data.forEach(function(element, index) {
                    let newTypeData = new Date(element[column]);
                    if (isNaN(newTypeData) == NaN) return false;
                    typeColumn[indexColumn] = 'date';
                    element[column] = newTypeData;
                });
            } else if (changeTo == "string") {
                data.forEach(function(element, index) {
                    let newTypeData = element[column].toString();
                    if (isNaN(newTypeData) == NaN) return;
                    typeColumn[indexColumn] = 'varchar(150)';
                    element[column] = newTypeData;
                });
            }
        }


        function getDB() {

            $.ajax({
                type: 'GET',
                url: '/get/database',
                success: function(data) {
                    let db = document.getElementById('db');
                    db.innerHTML = '';
                    let createTable = document.getElementById('createTableQuery');
                    createTable.value = ''
                    createTable.value += 'CREATE TABLE `' + fileName[0] + '` ( ';


                    for (i = 0; i < headerTable.length; i++) {
                        console.log(headerTable[i].toLowerCase());
                        console.log(headerTable[i]);
                        if (headerTable[i].toLowerCase() == 'id') {
                            createTable.value += headerTable[i] + ' ' + typeColumn[i] +
                            ' primary key not null, ';
                        } else if (headerTable.length - 1 == i) {
                            createTable.value += headerTable[i] + ' ' + typeColumn[i];
                        }
                         else {
                            createTable.value += headerTable[i] + ' ' + typeColumn[i] +
                            ', ';
                        }
                    }
                    createTable.value += ')';
                    db.innerHTML = '';
                    data.forEach(element =>
                        db.innerHTML += '<option value="' + element.Database + '">' + element.Database +
                        '</option>'
                    );

                },
            });
        }

        function addNewDatabase() {
            const addNewDatabase = document.getElementById('addNewDatabase').value;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/add/database',
                type: 'POST',
                data: {
                    name: addNewDatabase,
                },
                success: function(data) {

                    const messageAddDb = document.getElementById('messageAddDb');
                    messageAddDb.innerHTML = '<h4 class="text-green-500">Success</h4>'
                    const db = document.getElementById('db');
                    db.innerHTML += '<option value="' + addNewDatabase + '">' + addNewDatabase + '</option>'
                },

                error: function(response) {
                    const messageAddDb = document.getElementById('messageAddDb');
                    messageAddDb.innerHTML = '<h6 class="text-red-700">' + response.responseJSON.errors
                        .name +
                        '</h6>'
                }
            });
        }

        function createTableAndAddData() {
            let db = document.getElementById('db').value;
            const addTable = document.getElementById('createTableQuery').value;
            console.log(addTable);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/add/table',
                type: 'POST',
                data: {
                    db: db,
                    table: fileName,
                    tableQuery: addTable,
                    data: data,
                    headerTable: headerTable,

                },
                success: function(data) {
                    // console.log(data);
                    successMessage();
                    // delete success message after 4s
                    setTimeout(() => {
                        deleteSuccessMessage()
                    }, 4000);
                   var body =  document.getElementsByTag('body');
                   body.innerHTML = "<h1 class="bg-green-600">Success</h1>";

                },
                error: function(response) {
                  console.log( response.responseJSON.errors);
                }
            });
        }

        function successMessage() {
            const successMessage = document.getElementById('successMessage');
            successMessage.innerHTML =
                '<div class="absolute mt-20 mr-10 right-0 top-0 py-3 px-8 bg-green-500 text-white">Success</div>'
        }

        function deleteSuccessMessage() {
            const successMessage = document.getElementById('successMessage');
            successMessage.innerHTML = ''
        }
    </script>
@endsection
