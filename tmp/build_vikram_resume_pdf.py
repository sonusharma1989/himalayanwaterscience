from pathlib import Path
from reportlab.lib.pagesizes import letter
from reportlab.lib.colors import HexColor
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, HRFlowable, KeepTogether, PageBreak, Table, TableStyle

OUT = Path(r"E:\xampp\htdocs\himalayanwaterscience\output\pdf\Vikram_Singh_Rathore_Senior_Backend_Resume.pdf")
OUT.parent.mkdir(parents=True, exist_ok=True)

NAVY = HexColor("#17365D")
BLUE = HexColor("#1F4E79")
INK = HexColor("#202B38")
MUTED = HexColor("#586574")
LIGHT = HexColor("#D9E2F3")

doc = SimpleDocTemplate(str(OUT), pagesize=letter, rightMargin=0.55*inch, leftMargin=0.55*inch,
                        topMargin=0.42*inch, bottomMargin=0.42*inch,
                        title="Vikram Singh Rathore - Senior Backend Engineer Resume",
                        author="Vikram Singh Rathore")

styles = getSampleStyleSheet()
title = ParagraphStyle("ResumeTitle", fontName="Helvetica-Bold", fontSize=20, leading=22, textColor=NAVY,
                       alignment=TA_CENTER, spaceAfter=1)
tag = ParagraphStyle("Tag", fontName="Helvetica-Bold", fontSize=9.6, leading=11, textColor=BLUE,
                     alignment=TA_CENTER, spaceAfter=3)
contact = ParagraphStyle("Contact", fontName="Helvetica", fontSize=8.6, leading=10, textColor=MUTED,
                         alignment=TA_CENTER, spaceAfter=5)
body = ParagraphStyle("Body", fontName="Helvetica", fontSize=8.7, leading=10.3, textColor=INK,
                      alignment=TA_LEFT, spaceAfter=2)
skill = ParagraphStyle("Skill", parent=body, fontSize=8.5, leading=9.8, spaceAfter=0.8)
heading = ParagraphStyle("Heading", fontName="Helvetica-Bold", fontSize=10, leading=11, textColor=BLUE,
                         spaceBefore=4, spaceAfter=2, keepWithNext=True)
role_style = ParagraphStyle("Role", fontName="Helvetica-Bold", fontSize=9.3, leading=10.5, textColor=NAVY,
                            spaceBefore=2.5, spaceAfter=0, keepWithNext=True)
company_style = ParagraphStyle("Company", fontName="Helvetica-Bold", fontSize=8.6, leading=9.5, textColor=INK,
                               spaceAfter=1, keepWithNext=True)
bullet = ParagraphStyle("Bullet", parent=body, fontSize=8.45, leading=9.75, leftIndent=12, firstLineIndent=-6,
                        bulletIndent=0, spaceAfter=1.1)
footer_style = ParagraphStyle("Footer", fontName="Helvetica", fontSize=7.5, textColor=MUTED, alignment=TA_RIGHT)

story = []
story += [Paragraph("VIKRAM SINGH RATHORE", title),
          Paragraph("SENIOR BACKEND ENGINEER | TECH LEAD | NODE.JS &amp; PHP/LARAVEL", tag),
          Paragraph("+91-7014463427&nbsp;&nbsp; | &nbsp;&nbsp;vikramrathore66223@gmail.com&nbsp;&nbsp; | &nbsp;&nbsp;<link href='https://www.linkedin.com/in/vikram-singh-rathore-999bb7134/' color='#586574'>linkedin.com/in/vikram-singh-rathore-999bb7134</link>&nbsp;&nbsp; | &nbsp;&nbsp;Udaipur, India", contact)]

def sec(text):
    story.append(Paragraph(text.upper(), heading))
    story.append(HRFlowable(width="100%", thickness=0.65, color=LIGHT, spaceBefore=0, spaceAfter=2))

def p(text, style=body): story.append(Paragraph(text, style))
def b(text): story.append(Paragraph("- " + text, bullet))

def role(title_text, dates, company, location):
    t = Table([[Paragraph(title_text, role_style), Paragraph(dates, ParagraphStyle("Date", parent=role_style, textColor=MUTED, alignment=TA_RIGHT))],
               [Paragraph(company, company_style), Paragraph(location, ParagraphStyle("Loc", parent=company_style, fontName="Helvetica-Oblique", textColor=MUTED, alignment=TA_RIGHT))]],
              colWidths=[5.45*inch, 1.4*inch])
    t.setStyle(TableStyle([("VALIGN", (0,0), (-1,-1), "TOP"), ("LEFTPADDING", (0,0), (-1,-1), 0),
                           ("RIGHTPADDING", (0,0), (-1,-1), 0), ("TOPPADDING", (0,0), (-1,-1), 0),
                           ("BOTTOMPADDING", (0,0), (-1,-1), 0)]))
    story.append(t)

sec("Professional Summary")
p("Senior Backend Engineer and Tech Lead with 8+ years of experience delivering production systems across <b>Node.js/Express.js</b> and <b>PHP/Laravel</b>. Strong background in REST API design, backend architecture, MySQL/RDS optimization, authentication and authorization, payment workflows, AWS deployments, ERP/e-commerce platforms, and third-party integrations. Owns delivery from requirements and technical design through implementation, production release, troubleshooting, and team mentoring.")

sec("Core Technical Skills")
p("<b><font color='#17365D'>Backend:</font></b> Node.js, Express.js, JavaScript, PHP, Laravel, REST APIs, Webhooks, Microservices", skill)
p("<b><font color='#17365D'>Data:</font></b> MySQL, Amazon RDS, Prisma ORM; working exposure to MongoDB", skill)
p("<b><font color='#17365D'>Cloud &amp; Production:</font></b> AWS EC2, S3 and RDS, Linux, PM2, Apache, Git", skill)
p("<b><font color='#17365D'>Security &amp; Payments:</font></b> OTP authentication, JWT, RBAC, Razorpay, GST workflows", skill)
p("<b><font color='#17365D'>Integrations:</font></b> SAP ERP, Zendesk, WhatsApp/SMS, POS, E-Invoice, E-Waybill, Gemini and OpenAI APIs", skill)
p("<b><font color='#17365D'>Additional:</font></b> Next.js, React, Python, FastAPI, SQL, Jira", skill)

sec("Professional Experience")
role("Tech Lead / Team Lead - Backend & Full Stack", "Feb 2025 - Present", "Gyanitalk Technologies (OPC) Private Limited", "Udaipur, India")
b("Lead end-to-end engineering of an astrology and consultation platform, covering requirement analysis, backend architecture, API design, implementation, production deployment, and cross-functional delivery.")
b("Build and maintain backend services using Node.js, Express.js, PHP/Laravel, Prisma ORM, and MySQL within a mixed-service architecture.")
b("Designed secure authentication and authorization flows using OTP login, JWT tokens, and role-based access control for customer and operational workflows.")
b("Implemented Razorpay payment flows, webhook handling, GST calculation, and automated invoice generation for consultation transactions.")
b("Built a Python/FastAPI service that processes structured Kundli data and prepares context for Gemini and OpenAI-powered AI Astrologer workflows.")
b("Improve API and database performance through query review and Prisma/MySQL optimization; mentor developers and coordinate delivery with product, frontend, and QA teams.")

role("Senior Backend Developer", "Apr 2022 - Jan 2025", "Wooden Street Furnitures Pvt. Ltd.", "Udaipur, India")
b("Developed and maintained backend systems for high-volume e-commerce and ERP workflows using Laravel, Node.js, MySQL/RDS, REST APIs, and AWS infrastructure.")
b("Delivered APIs for product catalog, cart, checkout, order management, manufacturing, inventory, accounts, reporting, and order tracking.")
b("Integrated SAP ERP, Zendesk, payment gateways, WhatsApp/SMS services, POS systems, GST E-Invoice, and E-Waybill workflows.")
b("Improved database performance and production stability through SQL query optimization, indexing, and backend troubleshooting.")
b("Worked across business-critical modules and integration boundaries to translate operational requirements into reliable backend workflows.")

role("Senior Backend Developer", "Aug 2018 - Mar 2022", "Cognus Technology", "Udaipur, India")
b("Developed backend APIs and authentication systems for multiple client applications using Node.js, PHP/Laravel, and MySQL.")
b("Implemented integrations with payment gateways, SMS providers, and analytics services, including API contracts and error-handling workflows.")
b("Optimized database queries and API endpoints and collaborated with frontend engineers on clear, maintainable API contracts.")

sec("Selected Engineering Work")
role("Astrology, Consultation & AI Platform", "2025", "Node.js, Express.js, Laravel, Prisma, MySQL, FastAPI", "")
b("Delivered consultation, authentication, payments, GST invoicing, and AI-assisted astrology workflows across Node.js, Laravel, and FastAPI services.")
b("Integrated Gemini and OpenAI services through structured context preparation while keeping core platform workflows in the broader backend ecosystem.")

role("E-Commerce & Multi-Module ERP", "2022 - 2025", "Laravel, Node.js, MySQL/RDS, AWS, REST APIs", "")
b("Built and enhanced connected commerce and ERP workflows spanning catalog, checkout, orders, manufacturing, inventory, accounts, reporting, and external systems.")

sec("Education")
role("Bachelor of Technology (B.Tech), Computer Science Engineering", "2013 - 2017", "Rajasthan Technical University - SS College of Engineering", "Udaipur, India")

def footer(canvas, doc_obj):
    canvas.saveState()
    canvas.setFont("Helvetica", 7.5)
    canvas.setFillColor(MUTED)
    canvas.drawRightString(letter[0]-0.55*inch, 0.22*inch, f"Vikram Singh Rathore | Page {doc_obj.page}")
    canvas.restoreState()

doc.build(story, onFirstPage=footer, onLaterPages=footer)
print(OUT)
