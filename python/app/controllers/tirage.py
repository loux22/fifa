from flask import Blueprint, render_template, redirect, url_for, request
import mysql.connector


fifa = Blueprint('fifa', __name__, url_prefix='/')

@fifa.route('/')
def index():
    return redirect(url_for('fifa.tirage'))



@fifa.route('/tirage', methods=['GET', 'POST'])
def tirage():
    continent = request.args.get('continent')

    teams = get_team_filtre()

    if not continent:
        continent = "tous"
    if not continent == "tous" :
        teams = [dico for dico in teams if dico.get('continent') == continent]

    teams_select = get_team_select()
    print(teams_select)

    return render_template('pages/tirage.html', teams=teams, continent=continent)





def connexion_bdd():
    cnx = mysql.connector.connect(user='root', password='',
                                host='127.0.0.1',
                                database='fifa')
    return cnx

def get_team_filtre():
    teams = []
    cur = connexion_bdd()
    if cur and cur.is_connected():
        with cur.cursor(dictionary=True) as cursor:
            result = cursor.execute(f"SELECT * FROM equipe ORDER BY points desc")
            rows = cursor.fetchall()
            for index,row in enumerate(rows):
                row["classement"] = index + 1
                teams.append(row)
        cur.close()
        
    else:
        print("Could not connect")
    return teams

def get_team_select():
    teams_select = []
    if request.method == 'POST':
        n_team = request.form.get('n_team')
        team_select = request.form.getlist('team_select')
        for team in team_select:
            cur = connexion_bdd()
            with cur.cursor(dictionary=True) as cursor:
                result = cursor.execute(f"SELECT * FROM equipe WHERE nom_equipe='{team}'")
                team_tirage = cursor.fetchone()
                teams_select.append(team_tirage)
        cur.close()
    return teams_select
