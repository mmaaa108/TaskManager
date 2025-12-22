# ملاحظات تقنية - TaskManager Project

##  أكبر مشكلة واجهتني في Docker وكيف حللتها

### المشكلة
عند أول محاولة لتشغيل المشروع، واجهت مشكلة في الاتصال بقاعدة البيانات:

Database connection failed: SQLSTATE[HY000] [2002] Connection refused


### السبب
حاوية PHP كانت تحاول الاتصال بـ MySQL قبل أن تكون جاهزة.

### الحل
1. أضفت `depends_on: db` في docker-compose.yml
2. استخدمت اسم الخدمة `db` بدلاً من `localhost`
3. تأكدت من أن الحاويتين على نفس الـ network

### الدرس المستفاد
في Docker، الحاويات تتواصل باستخدام أسماء الخدمات وليس localhost.


##  أهم درس تعلمته في Git/GitHub

### أهمية الـ Commit Messages الواضحة
تعلمت كتابة رسائل commits وصفية:
- `feat: add task creation functionality`
- `docker: configure MySQL connection`
- `docs: add setup instructions`

بدلاً من "update" أو "fix".

### استخدام .gitignore بشكل صحيح
تعلمت استبعاد الملفات غير الضرورية مثل ملفات IDE وملفات النظام.

### بنية المجلدات المنظمة
تنظيم الملفات في مجلدات واضحة يجعل المشروع احترافياً.



##  ملاحظات إضافية

### ما تعلمته
- ربط PHP مع MySQL في Docker
- أهمية docker-compose لتشغيل خدمات متعددة
- كتابة Dockerfile فعال
- أهمية التوثيق الجيد

تاريخ الإنشاء: 22 ديسمبر 2024