# **0THE OPEN UNIVERSITY OF TANZANIA**

# 

# **THE INSTITUTE OF EDUCATIONAL AND MANAGEMENT TECHNOLOGIES**

## 

## 

## **SOFTWARE REQUIREMENTS SPECIFICATION (SRS)**

## **SHORT COURSES MANAGEMENT INFORMATION SYSTEM SCMIS**

| Project Title | SHORT COURSES MANAGEMENT INFORMATION SYSTEM |
| :---- | :---- |
| Prepared by | BEATUS WINIFRID MBUNDA EVODIUS ALBART MAHFOUDH KATAKWEBA |
| Organization | THE OPEN UNIVERSITY OF TANZANIA |
| Date Created | 1st February, 2023 |
| Date Reviewed |  |
| Version Approved | 1.0 |

| Approval | Name | Job Title/Role | Signature | Date |
| :---- | :---- | :---- | :---- | :---- |
| Approved by:  |  |  |  |  |

**Table of Contents**

[INTRODUCTION](#bookmark=id.6z80upn60k6f)	[3](#bookmark=id.6z80upn60k6f)

[Purpose](#bookmark=id.w1cu0blx80o)	[3](#bookmark=id.w1cu0blx80o)

[Intent Audience and Reading Suggestions](#bookmark=id.c0ir4h491alf)	[3](#bookmark=id.c0ir4h491alf)

[Product Scope](#bookmark=id.7egkzzqh6yt4)	[3](#bookmark=id.7egkzzqh6yt4)

[OVERALL DESCRIPTION OF THE PROJECT](#bookmark=id.cx1mv7vvbi4q)	[3](#bookmark=id.cx1mv7vvbi4q)

[Product Perspective](#bookmark=id.co35ozmowrfr)	[3](#bookmark=id.co35ozmowrfr)

[Product Functions](#bookmark=id.f20m76tx0ekd)	[3](#bookmark=id.f20m76tx0ekd)

[Key System Users](#bookmark=id.rlqy44ttg39c)	[4](#bookmark=id.rlqy44ttg39c)

[Operating Environment](#bookmark=id.ejrtpsh545f3)	[4](#bookmark=id.ejrtpsh545f3)

[User Documentation](#bookmark=id.izlfs41nmdbp)	[4](#bookmark=id.izlfs41nmdbp)

[EXTERNAL INTERFACE REQUIREMENTS](#bookmark=id.e5dmslpfucy0)	[4](#bookmark=id.e5dmslpfucy0)

[Hardware Interfaces](#bookmark=id.b0wtafn6gy36)	[4](#bookmark=id.b0wtafn6gy36)

[Software Interfaces](#bookmark=id.7ov6nti97uw2)	[4](#bookmark=id.7ov6nti97uw2)

[**FUNCTIONAL REQUIREMENTS**](#bookmark=id.ab4qlm6ogh8i)	[4](#bookmark=id.ab4qlm6ogh8i)

[NON-FUNCTIONAL REQUIREMENTS](#bookmark=id.1zpaify7zaw7)	[11](#bookmark=id.1zpaify7zaw7)

[Security Requirements](#bookmark=id.gulkgcihl30a)	[11](#bookmark=id.gulkgcihl30a)

[Confidentiality](#bookmark=id.daqd1h9ff4x7)	[12](#bookmark=id.daqd1h9ff4x7)

[Authentication](#bookmark=id.19e7aolup3rc)	[12](#bookmark=id.19e7aolup3rc)

[Log Audit/Traceability](#bookmark=id.vang4pzl1b6)	[12](#bookmark=id.vang4pzl1b6)

[Information Access](#bookmark=id.qnd604gchqna)	[12](#bookmark=id.qnd604gchqna)

[Access Mode](#bookmark=id.i8cj0lkyorkl)	[13](#bookmark=id.i8cj0lkyorkl)

[Integrity](#bookmark=id.brawbaba30cv)	[13](#bookmark=id.brawbaba30cv)

[Software Quality Attributes](#bookmark=id.mz9ldml8xijh)	[13](#bookmark=id.mz9ldml8xijh)

[Scalability](#bookmark=id.q17j20o6wj53)	[13](#bookmark=id.q17j20o6wj53)

[Usability](#bookmark=id.jjqsahwbukqq)	[13](#bookmark=id.jjqsahwbukqq)

[Response time](#bookmark=id.u9g9gg5kb6ry)	[14](#bookmark=id.u9g9gg5kb6ry)

[**APPENDIX B: CLASS DIAGRAM**](#heading)	[18](#heading)

1. # **INTRODUCTION**

   1. ## **Purpose**

The purpose of this document is to provide technical description of Short Courses Management Information System (SCMIS), its parameters and goals derived from requirement elicitation process that involved all stakeholders of the system within The Open University of Tanzania (OUT). This document also describes functional and non-functional requirements of SCMIS.

2. ## **Intent Audience and Reading Suggestions**

Developers of this system should make sure all requirements and specifications described in this SRS document are well-implemented during the development phase. Development team is advised to review this document from time to time to ensure it aligns with the gathered requirements.

3. ## **Product Scope**

Deliver working Short Course Management Information System (SCMIS) as per items in the requirement elicitation that will meet the current security standards to avoid possible cyber-attacks but also updating SCMIS to cater current growing information system needs which have been fueled by technological advancement as the endeavor to serve our stakeholders with excellence.

2. # **OVERALL DESCRIPTION OF THE PROJECT**

   1. ## **Product Perspective**

The system is expected to:

i.	To improve accessibility of files to staffs

ii.	Creating new files in short time

iii.	To reduce duplication of efforts and reduce cost for implementing similar project

iv.	Displaying all stored files

v.	Moving files between locations

vi.	Sorting files based on criteria such as date modified, date created, file size, file format

vii.	Increase availability of information to staffs

2. ## **Product Functions**

The major functions of the system are described in terms of modules. The following are high-level modules that reflect the functioning of the system:

- Course management

- User management

- Student management

- Teacher Management

- Centre management

- Payment management

  3. ## **Key System Users**

|  Index | Title | Abbreviation |
| :---- | :---- | :---- |
| 1 | Student | ST |
| 2 | Training Coordinator | TC |
| 3 | Systems administrator  | SA |
| 4 | Instructor | Instructor |
| 4 | Marketing Officer | MO |
| 5 | Director | DR |

  4. ## **Operating Environment**

The system shall operate with the following Web browsers: Microsoft Internet Explorer versions 7.0 and above, Mozilla Firefox 40.0 and above, and Google Chrome,etc. System shall operate on a server running the current versions on Linux Platform and latest version of Apache Web-Server, but also with a server of generation nine and above, with RAM 16GB and Hard disk of 2TB. 

5. ## **User Documentation**

There will be a user manual upon completion of the system. 

3. # **EXTERNAL INTERFACE REQUIREMENTS**

   1. ## **Hardware Interfaces**

The System shall operate on a server of generation nine and above, with RAM 16GB and Hard disk of 2TB.

2. ## **Software Interfaces**

The system shall operate with the following Web browsers: Microsoft Internet Explorer versions 7.0 and above, Mozilla Firefox 40.0 and above, and Google Chrome, etc. System shall operate on a server running the current versions on Linux Platform and latest version of Apache Web-Server.

4. # **FUNCTIONAL REQUIREMENTS**

This section presents the functional requirements of the system as illustrated in Table 1 below:

**Table 1: Functional Requirements of FTMIS**

| \#Ref | Description |  |
| :---- | :---- | :---- |
| R1 | Student ST |  |
|  |  | The system should allow Student to login |
|  |  | The system should allow Student to  enroll to Course |
|  |  | The system should allow Student to logout |
|  |  | The system should allow Student to view the profile |
|  |  | The system should allow Student to change password |
|  |  | The system should enable Student to update profile  |
|  |  | The system should allow Student to unenroll Course |
|  |  | The system should allow Student to generate invoice after Courses enrollment |
|  |  | The system should allow Student to generate Control number after Courses enrollment |
|  |  | The system should allow Student to make payment after Invoice generation |
|  |  | The system should allow Student to make payment after Control number generation |
|  |  | The system should allow Student to upload payment receipts after payment |
|  |  | The system should allow Student to receive alert payment overdue messages through email |
|  |  | The system should allow Student to view enrolled Courses |
|  |  | The system should allow Student to view payment receipt |
|  |  | The system should allow Student to view enrolled Courses |
|  |  | The system should allow Student to view Course Start & end time |
|  |  | The system should allow Registry Officer to print alert |

| \#Ref | Description |  |
| :---- | :---- | :---- |
| R2 | Training Manager (TC) |  |
|  |  | The system should allow TC to login |
|  |  | The system should allow TC to register a teacher |
|  |  | The system should allow TC to logout |
|  |  | The system should allow TC to view the profile |
|  |  | The system should allow TC to change password |
|  |  | The system should enable TC to register Course |
|  |  | The system should allow TC link course with teachers |
|  |  | The system should allow TC register Centre |
|  |  | The system should enable TC link course with centre |
|  |  | The system should enable TC to set course if face to face / online/both teaching method |
|  |  | The system should enable TC to send alert email informing teachers certificates are ready |
|  |  | The system should enable TC to view report of all on going courses in the regional center weekly/Monthly/Quarterly wise in the format of name of the centre, course name, total students per course, payment status ( full, half) |
|  |  | The system should allow TC to edit profile |

| \#Ref | Description |  |
| :---- | ----- | :---- |
| R3 | Systems administrator |  |
|  |  | The system should allow the SA to login |
|  |  | The system should prompt SA to change password |
|  |  | The system should allow SA to view profile |
|  |  | The system should allow SA to update profile |
|  |  | The system should allow SA to add a new system user |
|  |  | The system should allow SA to grant permission to users |
|  |  | The system should allow SA to view info of system users |
|  |  | The system should allow SA define role functions, Create role |
|  |  | The system should allow SA to block system user |
|  |  | The system should allow SA to unblock system user |
|  |  | The system should allow SA to search and filter system users |
|   |  | The system should allow the SA to manage users |

| \#Ref | Description |  |
| :---- | :---- | :---- |
| R4 | Marketing Officer (MO) |  |
|  |  | The system should allow MO to login |
|  |  | The system should allow MO to register Courses |
|  |  | The system should allow MO to logout |
|  |  | The system should allow MO to view the profile |
|  |  | The system should allow MO to change password |
|  |  | The system should enable MO view student with their courses  |
|  |  | The system should allow MO toview alert |
|  |  | The system should allow MO view payment |
|  |  | The system should allow MO to edit profile |
|  |  | The system should allow MO to login |
|  |  | The system should allow Dean to change password |
|  |  | The system should allow MO to logout |

| \#Ref | Description |  |
| :---- | :---- | :---- |
| R5 | Director  |  |
|  |  | The system should allow Director to login |
|  |  | The system should allow Director to view active classes |
|  |  | The system should allow Director to logout |
|  |  | The system should allow Director to view the profile |
|  |  | The system should allow Director to change password |
|  |  | The system should enable Director to view list of ready certificates |
|  |  | The system should allow Director to edit profile |

| \#Ref | Description |  |
| :---- | :---- | :---- |
| R6 | Instructor  |  |
|  |  | The system should allow Instructor to login  |
|  |  | The system should allow Instructor to change password |
|  |  | The system should allow Instructor to view profile |
|  |  | The system should allow Instructor to view enrolled students per course in the specific center |
|  |  | The system should allow Instructor to logout of the system |
|  |  | The system should allow Instructor to view paid students |
|  |  | The system should allow the Instructor to view course schedule |
|  |  | The system should allow Instructor to receive alert of email of certificate are ready |
|  |  | The system should allow Instructor to logout |

5. # **NON-FUNCTIONAL REQUIREMENTS**

   1. ## **Security Requirements**

The system to be developed shall enforce security requirements based on different controls such as preventive controls, detective controls, compensating controls, corrective controls and recovery controls as well as standards and best practices. All these controls shall work while maintaining the following:

1. ### **Confidentiality**

1. Sensitive data entered into the system must be encrypted by strong cryptographic techniques. 

2. The system shall provide the users with a secure way to change their passwords, whether when initializing a new account or by user request.

3. The system should use the HTTPS protocol in subsequent iterations in order to prevent unauthorized third-party viewing of the contents.

4.  The system shall provide access for authorized users while screening out those who do not need to view confidential data. 

5. The system shall provide transparent and automated security management of IDs, security policy enforcement and automated password resets. These features shall significantly reduce the on-going administration and management costs associated with web security.

   2. ### **Authentication**

6. First Login password generation followed by mandatory account password be changed on next login

7. Passwords must adhere to complex password rules including encryption 

8. Credential Control

9. Password Expiry (3 months Max)

10. Password Reset (automated)

11. Maximum number of login failures (4 times) followed by inactivity (5 minutes)

12. Minimum number of inactivity events (2 times) followed by account lockout

13. Issue (automated) alert on inactivity events

    3. ### **Log Audit/Traceability**

14. Provide log records for audit and traceability on authentication violation

    4. ### **Information Access**

15. Provide authorized access on role based and need to know basis

16. Provide information access (read, write, execute) right requests with respect to account role/privilege

17. Roles mapped with Groups

18. Session Life Cycle

19. Limit session inactivity/idleness (5 minutes)

20. Limit concurrent account access i.e., inactivate any other live sessions should another session get 

    5. ### **Access Mode**

21. Allow only web access to all users

22. Allow SSH only from Facility Internal Network or via the VPN for remote access only to Facility System Administrator

    6. ### **Integrity** 

23. The system shall be shown to be capable of maintaining the integrity of all the data which it controls and makes it available.

24. Integrity testing shall form part of the system acquisition and acceptance process

25. The transaction history should be maintained 

26. The system should retain partial data from interrupted entry for 15 minutes

    2. ## **Software Quality Attributes**

       1. ### **Scalability**

27. The system shall be designed to allow expansion through additional web and mobile applications.

28. The system shall be “device aware” and vary content and access based on which device a user is utilizing i.e., users can securely access the system via alternate devices, such as handheld PDAs and mobile phones.

29. It will have the capability to integrate with more stakeholders that are not specified for the time being. 

    2. ###  **Usability**

30. New visitors should not spend much time understanding how to use the system. It should be easy to use and navigate from page to page.

31. Other tools like search options and alphabetical arrangement of items can be implemented to help users obtain specific information easily and quickly.

32. Further the web interface of the system will be designed to be concise and user-friendly, with a well-designed graphical interface to help users identify the proper choice on the screen. 

33. An online help shall be provided for the users. 

34. Users are expected to be able to use the system productively with no training requiring only awareness of the availability of the system.

35. The system should provide interactive touch screen interface for ease and fast access

36. The average user learning time must be less than 1 day

37. The system should help users to avoid doing mistakes

38. The system should provide screen, mouse and keyboard navigation.

39. The system should be easy to navigate by using clear words, menus and drop-down lists.

    3. ### **Response time**

40. When retrieving information, it appears within 8 seconds, hence report will be release from the system as soon as it is needed

# 

