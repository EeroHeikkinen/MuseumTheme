@echo off
:: to_json.bat
:: Diagnostic program to display marc records.
:: $Id: to_json.bat
setlocal
::Get the current batch file's short path
for %%x in (%~f0) do set scriptdir=%%~dpsx
for %%x in (%scriptdir%) do set scriptdir=%%~dpsx
::echo BatchPath = %scriptdir%

if EXIST %scriptdir%SolrMarc.jar goto doit
pushd %scriptdir%..
for %%x in (%CD%) do set scriptdir=%%~sx\
popd

:doit

java -Dsolrmarc.main.class="org.solrmarc.marc.MarcPrinter" -jar %scriptdir%SolrMarc.jar to_json %1 %2 %3
