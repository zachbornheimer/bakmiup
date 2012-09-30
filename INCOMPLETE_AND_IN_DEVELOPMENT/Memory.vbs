' Memory.vbs
' Sample VBScript to discover how much RAM in computer
' Author Guy Thomas http://computerperformance.co.uk/
' Version 1.3 - August 2010
' ----------------------------------------------------'
' Modified by Z. Bornheimer for bakmiup
' August 2012

Option Explicit
Dim objWMIService, objComputer, colComputer
Dim strLogonUser, strComputer, objShell

strComputer = "."

Set objWMIService = GetObject("winmgmts:" _
& "{impersonationLevel=impersonate}!\\" _
& strComputer & "\root\cimv2")
Set colComputer = objWMIService.ExecQuery _
("Select * from Win32_ComputerSystem")

For Each objComputer in colComputer

' Z. Bornheimer's mod start
Wscript.Echo objComputer.TotalPhysicalMemory
Set objShell = WScript.CreateObject("WScript.Shell")
objShell.Run ("perl store.pl " & objComputer.TotalPhysicalMemory)

' Modifications Done.

Next

WScript.Quit

' End of free example of Memory WMI/VBScript
